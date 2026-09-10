---
name: revisar-pr-taguay
description: >-
  Revisa un diff, un PR o los cambios pendientes del proyecto Taguay
  (Laravel 11 + Blade/Bootstrap) contra el checklist de riesgos del equipo.
  Usar cuando pidan "revisá el PR", "revisá estos cambios", "está listo para
  mergear", "revisión previa al deploy" o similar. Produce una tabla ítem por
  ítem (cumplido / no cumplido / N/A) con archivo:línea y un veredicto final.
---

# Revisión de cambios — Taguay

Objetivo: revisar un conjunto de cambios contra `checklist-riesgos.md` (en esta
misma carpeta) y decir con fundamento si es apto para mergear.

Todo el output va en **español** (ver `CLAUDE.md` del proyecto).

## 1. Determinar qué diff se revisa

Según lo que haya pedido el usuario, en este orden:

1. **Rama base indicada** (ej. "revisá contra `main`"): 
   `git diff <rama-base>...HEAD`
2. **PR de GitHub** (si mencionan un número de PR y hay `gh` disponible):
   `gh pr diff <n>` y `gh pr view <n>`
3. **Sin argumento**: usar `main` como base. Si la rama actual *es* `main`,
   revisar los cambios sin commitear: `git diff HEAD` + archivos sin trackear
   relevantes (`git status --porcelain`).

Listá primero los archivos tocados (`git diff --stat`) y confirmá el alcance
antes de revisar en detalle.

## 2. Leer el checklist

Leé `checklist-riesgos.md` de esta carpeta. Es la lista de control obligatoria.
Si el usuario pide enfocarse en un área (p. ej. "solo seguridad"), priorizala
pero igual reportá el resto.

## 3. Evaluar cada ítem

Para cada ítem del checklist, asigná un estado:

- ✅ **Cumplido** — el diff lo respeta (o no lo afecta y estaba bien).
- ❌ **No cumplido** — hay una violación concreta. Indicá `archivo:línea` y qué
  está mal.
- ⚠️ **A revisar** — algo dudoso que necesita criterio humano.
- **N/A** — el diff no toca nada relacionado con ese ítem.

No inventes líneas: citá ubicaciones que existan en el diff.

## 4. Formato de salida

```
## Revisión: <rama/PR> → <base>
Archivos: <n> cambiados (+X / -Y)

| # | Ítem | Estado | Ubicación | Nota |
|---|------|--------|-----------|------|
| 1 | ...  | ✅/❌/⚠️/N/A | archivo:línea | ... |

## Hallazgos que bloquean el merge
- ...  (vacío si no hay)

## Sugerencias (no bloqueantes)
- ...

## Veredicto: APTO PARA MERGE / NO APTO — <una línea de motivo>
```

## 5. Reglas

- No aprobás ni mergeás nada: solo recomendás. El merge lo hace una persona.
- No ejecutes `git commit`, `git push` ni `git merge` (política de `CLAUDE.md`).
- Si `checklist-riesgos.md` no está, decilo y usá el checklist que trae este
  archivo como referencia mínima, pero avisá que falta el oficial.
- Si el diff está vacío o no se puede resolver la rama base, pedí aclaración
  en vez de asumir.
