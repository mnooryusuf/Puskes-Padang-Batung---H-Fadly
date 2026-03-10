<?php
$content = file_get_contents('resources/views/filament/pages/laporan.blade.php');

$replacements = [
    "{{ \$this->getAction('cetak_lplpo') }}" => "{{ \$this->cetakLplpoAction }}",
    "{{ \$this->getAction('cetak_lra') }}" => "{{ \$this->cetakLraAction }}",
    "{{ \$this->getAction('cetak_kunjungan') }}" => "{{ \$this->cetakKunjunganAction }}",
    "{{ \$this->getAction('cetak_kunjungan_poli') }}" => "{{ \$this->cetakKunjunganPoliAction }}",
    "{{ \$this->getAction('cetak_pasien_baru') }}" => "{{ \$this->cetakPasienBaruAction }}",
    "{{ \$this->getAction('cetak_rekap_tindakan') }}" => "{{ \$this->cetakRekapTindakanAction }}",
    "{{ \$this->getAction('cetak_statistik_lab') }}" => "{{ \$this->cetakStatistikLabAction }}",
    "{{ \$this->getAction('cetak_pasien_status') }}" => "{{ \$this->cetakPasienStatusAction }}",
    "{{ \$this->getAction('cetak_obat_expired') }}" => "{{ \$this->cetakObatExpiredAction }}",
    "{{ \$this->getAction('cetak_obat_analisa') }}" => "{{ \$this->cetakObatAnalisaAction }}",
    "{{ \$this->getAction('cetak_pendapatan') }}" => "{{ \$this->cetakPendapatanAction }}",
    "{{ \$this->getAction('cetak_distribusi_penyakit') }}" => "{{ \$this->cetakDistribusiPenyakitAction }}",
    "{{ \$this->getAction('cetak_lb1') }}" => "{{ \$this->cetakLb1Action }}",
];

foreach ($replacements as $old => $new) {
    $content = str_replace($old, $new, $content);
}

file_put_contents('resources/views/filament/pages/laporan.blade.php', $content);
echo "Updated laporan.blade.php\n";
