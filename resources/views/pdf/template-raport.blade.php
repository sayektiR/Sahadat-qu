@php
    $student = $report->student;
    $branch = $report->branch;
    $period = $report->period;
    $guardian = $student?->guardian;
    $statusLabels = ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'];
    $averageScore = $assessments->avg('final_score');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapor {{ $student?->name }} - Sahadat-Qu</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #e8edf3;
            color: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }
        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 16px 24px;
        }
        .button {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #fff;
            color: #0f172a;
            cursor: pointer;
            font-weight: 700;
            padding: 10px 14px;
            text-decoration: none;
        }
        .page {
            width: 794px;
            min-height: 1123px;
            margin: 0 auto 32px;
            background: #fff;
            padding: 44px 48px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .12);
        }

        .header{
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .header-content{
            display:flex;
            align-items:center;
        }

        .header-logo{
            width:90px;
            text-align:center;
        }

        .header-logo img{
            width:75px;
            height:75px;
            object-fit:contain;
        }

        .header-text{
            flex:1;
            text-align:center;
            margin-right:90px;
        }

        .header-text h1{
            margin:0;
            font-size:13px;
            font-weight:bold;
        }

        .header-text h2{
            margin:2px 0;
            font-size:15px;
            font-weight:700;
            text-transform:uppercase;
        }

        .header-text h3{
            margin:2px 0;
            font-size:13px;
            font-weight:bold;
        }

        .header-text p{
            margin:0;
            font-size:11px;
        }

        .header-line{
            border-top:2px solid #000;
            border-bottom:1px solid #000;
            height:4px;
            margin-top:10px;
        }

        .report-title{
            margin-top:8px;
            text-align:center;
            font-size:15px;
            font-weight:bold;
            text-transform:uppercase;
        }

        .header-logo img{
            width:90px;
            height:90px;
            object-fit:contain;
        }
        .header-text h1{
            margin:0;
            font-size:22px;
            font-weight:700;
            color:#0f172a;
        }

        .header-text h2{
            margin:4px 0;
            font-size:20px;
            color:#172554;
        }

        .header-text p{
            margin:2px 0;
            font-size:15px;
            color:#475569;
        }
        .identity {
            display: grid;
            grid-template-columns: 132px 1fr 1fr;
            gap: 18px;
            margin-top: 26px;
            align-items: stretch;
        }
        .photo {
            display: flex;
            width: 132px;
            height: 132px;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #dbe1e8;
        }
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo svg {
            width: 82px;
            height: 82px;
            color: #1f2937;
        }
        .info-box {
            border: 1px solid #d6dde6;
            background: #f8fafc;
            padding: 12px;
        }
        .row {
            display: grid;
            grid-template-columns: 96px 1fr;
            gap: 8px;
            margin-bottom: 8px;
        }
        .row:last-child { margin-bottom: 0; }
        .label {
            color: #475569;
            font-weight: 700;
        }
        .value {
            color: #0f172a;
            font-weight: 700;
        }
        .section {
            margin-top: 22px;
        }
        .section-title {
            margin: 0 0 10px;
            border-left: 5px solid #172554;
            padding-left: 10px;
            font-size: 13px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #e2e8f0;
            color: #0f172a;
            font-size: 11px;
            text-align: left;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 9px;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .summary-card {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 10px;
        }
        .summary-card span {
            display: block;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .summary-card strong {
            display: block;
            margin-top: 5px;
            color: #172554;
            font-size: 18px;
        }
        .note {
            min-height: 86px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 14px;
        }
        .signature {
            display: flex;
            justify-content: flex-end;
            margin-top: 28px;
        }
        .signature-box {
            width: 220px;
            text-align: center;
        }
        .signature-space {
            height: 76px;
        }
        .muted { color: #64748b; }
        .print-hint {
            margin-right: auto;
            color: #475569;
            font-size: 12px;
            align-self: center;
        }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                box-shadow: none;
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <span class="print-hint">Gunakan tombol unduh untuk menyimpan halaman ini sebagai PDF.</span>
        <a href="{{ route('admin.reports') }}" class="button">Kembali</a>
        <button type="button" onclick="window.print()" class="button">Unduh ke PDF</button>
    </div>

    <main class="page">
        <header class="header">
            <div class="header-content">
                <div class="header-logo">
                    <img src="{{ asset('images/logo-sahadat-qu.jpeg') }}">
                </div>
                <div class="header-text">
                    <h1>YAYASAN RUMAH TAHFIDZ SAHADAT-QU</h1>
                    <h3>CABANG {{ strtoupper($branch->name) }}</h3>
                    <p>
                        {{ $branch->address }}
                    </p>
                    <p>
                        Telp. {{ $branch->phone }}
                    </p>
                </div>
            </div>
            <div class="header-line"></div>
            <div class="report-title">
                LAPORAN HASIL BELAJAR SANTRI
            </div>
        </header>

        <section class="identity">
            <div class="photo">
                @if ($student?->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->name }}">
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="8" r="3"/>
                        <path d="M5 21a7 7 0 0 1 14 0"/>
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                @endif
            </div>

            <div class="info-box">
                <div class="row"><span class="label">Nama</span><span class="value">{{ $student?->name }}</span></div>
                <div class="row"><span class="label">NIS</span><span class="value">{{ $student?->nis ?: '-' }}</span></div>
                <div class="row"><span class="label">Kelompok</span><span class="value">{{ $student?->group?->name ?: '-' }}</span></div>
                <div class="row"><span class="label">Alamat</span><span class="value">{{ $student?->address ?: '-' }}</span></div>
            </div>

            <div class="info-box">
                <div class="row"><span class="label">Periode</span><span class="value">{{ $period?->name }} - {{ $period?->semester }}</span></div>
                <div class="row"><span class="label">Wali</span><span class="value">{{ $guardian?->name ?: '-' }}</span></div>
                <div class="row"><span class="label">Guru</span><span class="value">{{ $report->homeroomTeacher?->name ?: '-' }}</span></div>
                <div class="row"><span class="label">Tanggal</span><span class="value">{{ $report->report_date?->format('d M Y') }}</span></div>
            </div>
        </section>

        {{-- <section class="section">
            <h2 class="section-title">Penilaian Hafalan</h2>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Jenis</th>
                        <th>Surah</th>
                        <th>Ayat</th>
                        <th>Nilai</th>
                        <th>Predikat</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($memorizationAssessments as $assessment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $assessment->memorizationAssessment?->memorization_type ?: '-' }}</td>
                            <td>{{ $assessment->memorizationAssessment?->surah ?: '-' }}</td>
                            <td>{{ $assessment->memorizationAssessment?->from_ayah }} - {{ $assessment->memorizationAssessment?->to_ayah }}</td>
                            <td>{{ number_format((float) $assessment->final_score, 1) }}</td>
                            <td>{{ $assessment->predicate ?: '-' }}</td>
                            <td>{{ $assessment->note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="muted">Data penilaian hafalan belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section> --}}

        <section class="section">
            <h2 class="section-title">Penilaian Mata Pelajaran dan Kehadiran</h2>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Mata Pelajaran</th>
                        <th>Aspek</th>
                        <th>Nilai</th>
                        <th>Predikat</th>
                        <th>Catatan Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $assessment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            {{-- <td>{{ $assessment->subjects ?: '-' }}</td> --}}
                            <td>{{ $assessment->template?->name ?: '-' }}</td>
                            <td>
                                @foreach($assessment->scorings as $scoring)
                                    {{ $scoring->aspect?->aspect_name ?? '-' }}
                                    ({{ $scoring->value }})<br>
                                @endforeach
                            </td>
                            <td>{{ number_format($assessment->final_score, 1) }}</td>
                            <td>{{ $assessment->predicate ?: '-' }}</td>
                            <td>{{ $assessment->note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="muted">Data penilaian mata pelajaran belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="summary-grid">
                @foreach ($statusLabels as $status => $label)
                    <div class="summary-card">
                        <span>{{ $label }}</span>
                        <strong>{{ $attendanceSummary[$status] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Rekap Nilai</h2>
            <div class="summary-grid">
                <div class="summary-card">
                    <span>Rata-rata Nilai</span>
                    <strong>
                        {{ $averageScore !== null ? number_format($averageScore, 1) : '-' }}
                    </strong>
                </div>
                <div class="summary-card"><span>Total Presensi</span><strong>{{ $attendanceSummary->sum() }}</strong></div>
                <div class="summary-card"><span>Predikat Umum</span><strong>{{ $averageScore !== null ? App\Models\Assessment::predicateFor($averageScore): '-'}}</strong></div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Catatan dari Ustadz/Ustadzah</h2>
            <div class="note">{{ $report->final_note ?? '' }}</div>
        </section>

        <section class="signature">
            <div class="signature-box">
                <p>{{ $branch?->address ?: 'Bojonegoro' }}, {{ $report->report_date?->format('d M Y') }}</p>
                <p>Pengurus Cabang</p>
                <div class="signature-space"></div>
                <strong>{{ $report->signed_by ?: 'Sahadat-Qu' }}</strong>
            </div>
        </section>
    </main>

    @if (request()->boolean('print'))
        <script>
            window.addEventListener('load', () => window.print());
        </script>
    @endif
</body>
</html>
