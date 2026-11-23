<?php
/**
 * El furgón de Tina Rodante dibujado (SVG decorativo). @var string $clase
 */
$clase ??= '';
?>
<svg class="tr-furgon <?= m_e($clase) ?>" viewBox="0 0 360 180" aria-hidden="true" focusable="false">
  <g class="tr-furgon__burbujas" fill="none" stroke="#9ed8f2" stroke-width="3">
    <circle cx="96" cy="22" r="9"/><circle cx="120" cy="10" r="5"/><circle cx="140" cy="26" r="7"/><circle cx="70" cy="14" r="4"/>
  </g>
  <path d="M22 132V64c0-17 13-30 30-30h168c11 0 20 5 27 13l38 44c5 6 14 10 27 13 11 3 17 10 17 20v16c0 7-5 12-12 12H34c-7 0-12-5-12-12z" fill="#ffc83a" stroke="#10392c" stroke-width="5" stroke-linejoin="round"/>
  <path d="M218 46h4c7 0 13 3 18 8l30 35H218z" fill="#9ed8f2" stroke="#10392c" stroke-width="5" stroke-linejoin="round"/>
  <path d="M22 116h306" stroke="#10392c" stroke-width="12"/>
  <path d="M196 46v84" stroke="#10392c" stroke-width="4"/>
  <circle cx="164" cy="80" r="22" fill="#9ed8f2" stroke="#10392c" stroke-width="5"/>
  <path d="M151 86c3-10 8-15 13-15s10 5 13 15c-4 4-8 6-13 6s-9-2-13-6z" fill="#f6f2e7"/>
  <path d="M153 76c-3-4-3-9 0-12M175 76c3-4 3-9 0-12" stroke="#f6f2e7" stroke-width="5" stroke-linecap="round"/>
  <circle cx="159.5" cy="79" r="1.8" fill="#10392c"/><circle cx="168.5" cy="79" r="1.8" fill="#10392c"/>
  <text x="38" y="74" class="tr-furgon__texto">TINA</text>
  <text x="38" y="98" class="tr-furgon__texto">RODANTE</text>
  <rect x="300" y="100" width="18" height="8" rx="3" fill="#f6f2e7" stroke="#10392c" stroke-width="3"/>
  <g class="tr-furgon__rueda"><circle cx="86" cy="146" r="22" fill="#10392c"/><circle cx="86" cy="146" r="9" fill="#f6f2e7"/><path d="M86 137v18M77 146h18" stroke="#10392c" stroke-width="3"/></g>
  <g class="tr-furgon__rueda"><circle cx="282" cy="146" r="22" fill="#10392c"/><circle cx="282" cy="146" r="9" fill="#f6f2e7"/><path d="M282 137v18M273 146h18" stroke="#10392c" stroke-width="3"/></g>
</svg>
