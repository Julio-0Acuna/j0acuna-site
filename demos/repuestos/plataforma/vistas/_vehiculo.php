<?php
/**
 * Vehículo elegido en Cardán (patente de ejemplo o tipo, año y motor). Se lee de ?patente= o ?tipo=&anio=&cc=,
 * se valida y queda en la sesión para seguir filtrando el catálogo; ?vehiculo=quitar lo borra.
 * Devuelve ['activo', 'tipo', 'anio', 'cc', 'texto', 'patente', 'aviso', 'aviso_tipo', 'patente_previa'].
 */
function cd_vehiculo(array $cfg): array
{
    static $v = null;
    if ($v !== null) {
        return $v;
    }
    $v = ['activo' => false, 'tipo' => '', 'anio' => 0, 'cc' => '', 'texto' => '', 'patente' => '', 'aviso' => '', 'aviso_tipo' => '', 'patente_previa' => ''];
    $armar = static function (string $tipo, int $anio, string $cc): string {
        $partes = [];
        if ($tipo !== '') {
            $partes[] = RP_TIPOS[$tipo];
        }
        if ($cc !== '') {
            $partes[] = $cc;
        }
        if ($anio > 0) {
            $partes[] = (string) $anio;
        }
        return implode(' · ', $partes);
    };

    if (($_GET['vehiculo'] ?? '') === 'quitar') {
        unset($_SESSION['cd_vehiculo']);
        return $v;
    }

    $patenteCruda = rp_linea($_GET['patente'] ?? '', 12);
    if ($patenteCruda !== '') {
        $p = rp_patente_normalizar($patenteCruda);
        if (!rp_patente_valida($p)) {
            $v['aviso'] = 'Esa patente no tiene el formato chileno: 4 letras y 2 números (BB·CD·12) o 2 letras y 4 números (AB·12·34).';
            $v['aviso_tipo'] = 'error';
            $v['patente_previa'] = $patenteCruda;
            return $v;
        }
        $perfil = $cfg['patentes_demo'][$p] ?? null;
        if (!is_array($perfil)) {
            $v['aviso'] = 'En esta demo solo reconocemos las patentes de ejemplo. Elige tu vehículo por tipo, año y motor, o consúltanos por WhatsApp con la patente ' . rp_patente_formato($p) . '.';
            $v['aviso_tipo'] = 'info';
            $v['patente'] = $p;
            $v['patente_previa'] = rp_patente_formato($p);
            return $v;
        }
        $v = array_replace($v, ['activo' => true, 'tipo' => $perfil['tipo'], 'anio' => (int) $perfil['anio'], 'cc' => $perfil['cc'], 'texto' => $perfil['texto'], 'patente' => $p]);
        $_SESSION['cd_vehiculo'] = ['tipo' => $v['tipo'], 'anio' => $v['anio'], 'cc' => $v['cc'], 'texto' => $v['texto'], 'patente' => $p];
        return $v;
    }

    $datos = null;
    if (isset($_GET['tipo']) || isset($_GET['anio']) || isset($_GET['cc'])) {
        $datos = ['tipo' => $_GET['tipo'] ?? '', 'anio' => $_GET['anio'] ?? '', 'cc' => $_GET['cc'] ?? '', 'patente' => ''];
    } elseif (is_array($_SESSION['cd_vehiculo'] ?? null)) {
        $datos = $_SESSION['cd_vehiculo'];
    }
    if ($datos === null) {
        return $v;
    }
    $tipo = is_string($datos['tipo'] ?? null) && isset(RP_TIPOS[$datos['tipo']]) ? $datos['tipo'] : '';
    $anioCrudo = trim((string) (is_scalar($datos['anio'] ?? null) ? $datos['anio'] : ''));
    $anio = ctype_digit($anioCrudo) && (int) $anioCrudo >= 1970 && (int) $anioCrudo <= (int) date('Y') + 1 ? (int) $anioCrudo : 0;
    $cc = is_string($datos['cc'] ?? null) && preg_match('/^[1-4]\.\d$/', $datos['cc']) ? $datos['cc'] : '';
    if ($tipo === '' && $anio === 0 && $cc === '') {
        unset($_SESSION['cd_vehiculo']);
        return $v;
    }
    $patente = is_string($datos['patente'] ?? null) && rp_patente_valida($datos['patente']) ? $datos['patente'] : '';
    $texto = $patente !== '' && is_string($datos['texto'] ?? null) ? $datos['texto'] : $armar($tipo, $anio, $cc);
    $v = array_replace($v, ['activo' => true, 'tipo' => $tipo, 'anio' => $anio, 'cc' => $cc, 'texto' => $texto, 'patente' => $patente]);
    $_SESSION['cd_vehiculo'] = ['tipo' => $tipo, 'anio' => $anio, 'cc' => $cc, 'texto' => $texto, 'patente' => $patente];
    return $v;
}

/** Cilindradas del buscador por vehículo. */
const CD_CILINDRADAS = ['1.0', '1.2', '1.4', '1.6', '1.8', '2.0', '2.2', '2.4', '2.5', '2.8', '3.0'];
