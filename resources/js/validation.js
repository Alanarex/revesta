/* Global form validation messages (French + customizable)
   This script replaces native browser validation messages with
   French defaults and supports per-field overrides via data-* attrs.

   Usage examples on an input:
   <input required data-error-value-missing="Entrez votre nom"> 
   <input type="email" required data-error-type-mismatch="Email invalide">
*/

const defaultMessages = {
  valueMissing: 'Veuillez remplir ce champ.',
  typeMismatch: {
    email: 'Veuillez entrer une adresse e-mail valide.',
    url: 'Veuillez entrer une URL valide.'
  },
  tooShort: 'La saisie est trop courte.',
  tooLong: 'La saisie est trop longue.',
  patternMismatch: 'Le format est invalide.',
  rangeUnderflow: 'La valeur est trop faible.',
  rangeOverflow: 'La valeur est trop élevée.',
  stepMismatch: 'La valeur n\'est pas valide.',
  badInput: 'Entrée non valide.',
  default: 'Veuillez corriger ce champ.'
};

function getCustomAttr(el, key) {
  // data-error-value-missing, data-error-type-mismatch, etc.
  return el.getAttribute(`data-error-${key}`) || null;
}

function resolveMessage(el) {
  const v = el.validity;
  if (!v) return defaultMessages.default;

  if (v.valueMissing) {
    return getCustomAttr(el, 'value-missing') || defaultMessages.valueMissing;
  }
  if (v.typeMismatch) {
    const custom = getCustomAttr(el, 'type-mismatch');
    if (custom) return custom;
    const t = (el.getAttribute('type') || '').toLowerCase();
    return defaultMessages.typeMismatch[t] || defaultMessages.default;
  }
  if (v.tooShort) return getCustomAttr(el, 'too-short') || defaultMessages.tooShort;
  if (v.tooLong) return getCustomAttr(el, 'too-long') || defaultMessages.tooLong;
  if (v.patternMismatch) return getCustomAttr(el, 'pattern-mismatch') || defaultMessages.patternMismatch;
  if (v.rangeUnderflow) return getCustomAttr(el, 'range-underflow') || defaultMessages.rangeUnderflow;
  if (v.rangeOverflow) return getCustomAttr(el, 'range-overflow') || defaultMessages.rangeOverflow;
  if (v.stepMismatch) return getCustomAttr(el, 'step-mismatch') || defaultMessages.stepMismatch;
  if (v.badInput) return getCustomAttr(el, 'bad-input') || defaultMessages.badInput;
  return defaultMessages.default;
}

function onInvalidEvent(e) {
  const el = e.target;
  if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement)) return;
  const msg = resolveMessage(el);
  el.setCustomValidity(msg);
}

function onInputEvent(e) {
  const el = e.target;
  if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement)) return;
  // Clear custom validity as user types; browser will re-evaluate validity
  if (el.validationMessage) {
    el.setCustomValidity('');
  }
}

function initGlobalValidation() {
  // Use capture so we catch native invalid events on children
  document.addEventListener('invalid', onInvalidEvent, true);
  document.addEventListener('input', onInputEvent, true);

  // For dynamically added forms/inputs, also clear custom validity on focus
  document.addEventListener('focusin', (e) => {
    const el = e.target;
    if (el && el.setCustomValidity) el.setCustomValidity('');
  });
}

// Initialize on DOMContentLoaded if ready, otherwise immediately if already loaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initGlobalValidation);
} else {
  initGlobalValidation();
}

export default {};
