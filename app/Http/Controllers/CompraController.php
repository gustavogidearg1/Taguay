<?php

namespace App\Http\Controllers;

use App\Mail\CompraNotificacion;
use App\Models\Compra;
use App\Models\SubCompra;
use App\Models\Campania;
use App\Models\CondicionPago;
use App\Models\Moneda;
use App\Models\Organizacion;
use App\Models\Producto;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CompraController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth']);
  }

  public function index(Request $r)
  {
$q = trim((string)$r->get('q',''));

$compras = Compra::with(['organizacion','campania','moneda'])
  ->when($q !== '', function($qq) use ($q) {

    // ✅ si es número, buscar por id exacto
    if (ctype_digit($q)) {
      $qq->where('id', (int)$q);
      return;
    }

    // ✅ si no, buscar por organización (como ya tenías)
    $qq->whereHas('organizacion', function($o) use ($q){
      $o->where('name','like',"%{$q}%")
        ->orWhere('codigo','like',"%{$q}%");
    });

  })
  ->orderByDesc('id')
  ->paginate(15)
  ->withQueryString();

    return view('compras.index', compact('compras','q'));
  }

  public function create()
  {
    $campanias = Campania::where('activo', true)->orderByDesc('id')->get();
    $condiciones = CondicionPago::where('activo', true)->orderBy('name')->get();
    $monedas = Moneda::orderBy('name')->get();

    // para modal organización (sin API)
    $organizaciones = Organizacion::where('activo', true)->orderBy('name')->get();

    // para modal productos (sin API)
    $productos = Producto::where('activo', true)->orderBy('name')->get();
    $unidades  = Unidad::orderBy('name')->get(); // por si querés dropdown manual

    $defaultMonedaId = Moneda::where('name','Dolar')->orWhere('name','Dólar')->value('id');

    return view('compras.create', compact(
      'campanias','condiciones','monedas','organizaciones','productos','unidades','defaultMonedaId'
    ));
  }

  public function store(Request $r)
{
  $data = $this->validateCompra($r, null);

  // normalizar: si viene vacío, guardamos NULL (para no chocar con UNIQUE)
  if (array_key_exists('codigo', $data)) {
    $data['codigo'] = trim((string)($data['codigo'] ?? ''));
    $data['codigo'] = $data['codigo'] !== '' ? $data['codigo'] : null;
  }

  return DB::transaction(function () use ($r, $data) {

    $subs = $data['sub_compras'] ?? [];
    unset($data['sub_compras']);

    $data['user_id'] = $r->user()->id;

    $compra = Compra::create($data);

    $this->syncSubCompras($compra, $subs);

    //$this->sendCompraMail($compra->fresh()->load($this->relations()), 'creada');

    return redirect()->route('compras.show', $compra)
      ->with('success', 'Compra creada correctamente.');
  });
}

  public function show(Compra $compra)
  {
    $compra->load($this->relations());
    return view('compras.show', compact('compra'));
  }

  public function edit(Compra $compra)
  {
    $compra->load(['subCompras.producto','subCompras.unidad','subCompras.moneda']);

    $campanias = Campania::where('activo', true)->orderByDesc('id')->get();
    $condiciones = CondicionPago::where('activo', true)->orderBy('name')->get();
    $monedas = Moneda::orderBy('name')->get();
    $organizaciones = Organizacion::where('activo', true)->orderBy('name')->get();
    $productos = Producto::where('activo', true)->orderBy('name')->get();
    $unidades  = Unidad::orderBy('name')->get();

    return view('compras.edit', compact(
      'compra','campanias','condiciones','monedas','organizaciones','productos','unidades'
    ));
  }

  public function update(Request $r, Compra $compra)
{
  $data = $this->validateCompra($r, $compra);

  if (array_key_exists('codigo', $data)) {
    $data['codigo'] = trim((string)($data['codigo'] ?? ''));
    $data['codigo'] = $data['codigo'] !== '' ? $data['codigo'] : null;
  }

  return DB::transaction(function () use ($r, $compra, $data) {

    $subs = $data['sub_compras'] ?? [];
    unset($data['sub_compras']);

    $compra->update($data);

    $this->syncSubCompras($compra, $subs);

    //$this->sendCompraMail($compra->fresh()->load($this->relations()), 'actualizada');

    return redirect()->route('compras.show', $compra)
      ->with('success', 'Compra actualizada correctamente.');
  });
}

  public function destroy(Compra $compra)
  {
    $compra->delete();
    return redirect()->route('compras.index')->with('success', 'Compra eliminada.');
  }


  // ===================== helpers =====================

  private function relations(): array
  {
    return ['organizacion','campania','condicionPago','moneda','monedaFin','subCompras.producto','subCompras.unidad','subCompras.moneda','user'];
  }

  private function validateCompra(Request $r, ?Compra $compra): array
  {
    $id = $compra?->id;

    $data = $r->validate([
      'fecha' => ['required','date'],
      'fecha_entrega' => ['nullable','date'],

      'organizacion_id' => ['required','exists:organizaciones,id'],
      'campania_id' => ['required','exists:campanias,id'],
      'condicion_pago_id' => ['required','exists:condicion_pagos,id'],

      'momento_pago' => ['nullable','date'],

      'codigo' => ['nullable','string','max:50', Rule::unique('compras','codigo')->ignore($id)],

      'moneda_id' => ['required','exists:monedas,id'],
      'moneda_fin_id' => ['nullable','exists:monedas,id'],

      'tasa_financ' => ['nullable','numeric','min:0'],

      'activo' => ['nullable'],
      'lugar_entrega' => ['nullable','string','max:100'],
      'obs' => ['nullable','string','max:200'],

      // sub tabla (mínimo 1 línea)
      'sub_compras' => ['required','array','min:1'],
      'sub_compras.*.id' => ['nullable','integer'],
      'sub_compras.*.producto_id' => ['required','exists:productos,id'],
      'sub_compras.*.cantidad' => ['required','numeric','min:0'],
      'sub_compras.*.unidad_id' => ['required','exists:unidades,id'],
      'sub_compras.*.precio' => ['required','numeric','min:0'],

      'sub_compras.*.moneda_id' => ['required','exists:monedas,id'],
      'sub_compras.*.fecha_venc' => ['nullable','date'],

      'sub_compras.*.bonificacion_1' => ['nullable','numeric','min:0'],
      'sub_compras.*.bonificacion_2' => ['nullable','numeric','min:0'],
      'sub_compras.*.bonificacion_3' => ['nullable','numeric','min:0'],
    ]);

    // checkbox
    $data['activo'] = $r->boolean('activo', true);

    // normalizar código
if (array_key_exists('codigo', $data)) {
  $data['codigo'] = trim((string)($data['codigo'] ?? ''));
  $data['codigo'] = $data['codigo'] !== '' ? $data['codigo'] : null;
}

    return $data;
  }

  private function syncSubCompras(Compra $compra, array $rows): void
  {
    // recalcular sub_total = cantidad * precio * (1 - bon1) * (1 - bon2) * (1 - bon3)
    $ids = [];

    foreach ($rows as $row) {
      $bon1 = (float)($row['bonificacion_1'] ?? 0);
      $bon2 = (float)($row['bonificacion_2'] ?? 0);
      $bon3 = (float)($row['bonificacion_3'] ?? 0);

      $cant = (float)($row['cantidad'] ?? 0);
      $precio = (float)($row['precio'] ?? 0);

      $factor = (1 - $bon1) * (1 - $bon2) * (1 - $bon3);
      $subTotal = round($cant * $precio * $factor, 2);

      $payload = [
        'producto_id' => $row['producto_id'],
        'cantidad' => $cant,
        'unidad_id' => $row['unidad_id'],
        'precio' => $precio,
        'moneda_id' => $row['moneda_id'],
        'fecha_venc' => $row['fecha_venc'] ?? null,
        'bonificacion_1' => $bon1,
        'bonificacion_2' => $bon2,
        'bonificacion_3' => $bon3,
        'sub_total' => $subTotal,
      ];

      if (!empty($row['id'])) {
        $sub = $compra->subCompras()->where('id', $row['id'])->first();
        if ($sub) {
          $sub->update($payload);
          $ids[] = $sub->id;
        }
      } else {
        $sub = $compra->subCompras()->create($payload);
        $ids[] = $sub->id;
      }
    }

    // borrar los eliminados del form
    $compra->subCompras()->whereNotIn('id', $ids)->delete();
  }

  private function sendCompraMail(Compra $compra, string $action): void
  {
    try {
      $to = auth()->user()->email;

      $ccRaw = (string) env('COMPRAS_MAIL_CC', '');
      $ccList = collect(preg_split('/[;,]+/', $ccRaw))
        ->map(fn($v)=>trim($v))
        ->filter(fn($v)=>$v !== '' && filter_var($v, FILTER_VALIDATE_EMAIL))
        ->values()->all();

      $mail = Mail::to($to);
      if (!empty($ccList)) $mail->cc($ccList);

      $mail->send(new CompraNotificacion($compra, $action));

    } catch (\Throwable $e) {
      Log::error('Error enviando mail de compra: '.$e->getMessage(), [
        'compra_id' => $compra->id ?? null,
        'to' => $to ?? null,
      ]);
    }
  }
}
