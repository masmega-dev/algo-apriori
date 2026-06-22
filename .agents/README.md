# .agents Guide

Folder `.agents` berisi fondasi default support agent untuk semua project baru Sandi. Tujuannya agar agent tidak mulai dari nol setiap kali ada inisialisasi project, dan sejak awal sudah punya standar engineering, security, testing, scaling, workflow, dan preferensi stack yang konsisten.

## Tujuan Utama
Gunakan folder ini sebagai baseline saat:
- memulai project baru
- menyusun arsitektur awal
- menggali kebutuhan website atau sistem
- memecah modul dan backlog
- mengerjakan fitur lanjutan
- menjaga kualitas code, security, testing, dan kesiapan operasional

## Preferensi Teknologi Default
Prioritas stack default:
1. Laravel
2. React + TypeScript
3. JavaScript/TypeScript
4. Golang
5. PHP non-Laravel bila diperlukan
6. CodeIgniter hanya untuk legacy atau permintaan eksplisit

Preferensi tambahan:
- UI default: biru dan putih
- Visual default: clear, glossy blur, sedikit gradasi, matte, lebih cerah, dan menarik
- Dark mode tetap didukung dengan kontras yang baik
- Arsitektur default: monolith rapi dulu, baru dipisah jika ada alasan kuat
- Jika frontend dan backend dipisah: frontend `Next.js Pages Router`, backend `Go Fiber`, auth `Auth.js`
- React default memakai `shadcn/ui` sebagai basis component system
- Jika Blade dipakai, lebih utamakan reusable custom Blade components dengan Tailwind dan Alpine
- Database default: MySQL atau PostgreSQL
- Komponen umum seperti modal, button, tabs, datatable, input, dan sejenisnya lebih diutamakan dibuat reusable internal component agar tinggal dipakai lintas halaman

## Struktur Folder
```text
.agents/
├── README.md
├── system_promt.md
├── rules/
└── workflow/
```

## File Inti
### [system_promt.md](/system_promt.md)
Persona dan kecerdasan default agent. File ini menetapkan role utama, prioritas stack, area expertise, preferensi visual, reusable component mindset, auth/session awareness, dan quality bar.

## Rules
### [default-stack-priority.md](/rules/default-stack-priority.md)
Preferensi stack utama, anti-pattern default, preferensi split architecture, dan arah arsitektur awal.

### [engineering-standards.md](/rules/engineering-standards.md)
Standar coding umum untuk Laravel, React, TypeScript, Golang, PHP, security, reusable architecture, clean/DRY code, dan quality gate dasar.

### [ui-frontend.md](/rules/ui-frontend.md)
Aturan frontend default, termasuk React/TypeScript, Next.js Pages Router, `shadcn/ui`, Blade custom components, UI quality, preferensi warna biru-putih, glossy blur, subtle gradient, matte feel, dark mode, dan reusable component direction.

### [backend-api.md](/rules/backend-api.md)
Standar desain API, HTTP method semantics, validation, response contract, authorization, session security non-built-in, dan performance baseline untuk Laravel/Go backend.

### [testing-strategy.md](/rules/testing-strategy.md)
Gaya testing default: prioritas area yang harus diuji, pendekatan Laravel/React/Go, serta quality gate test.

### [security-scale-ops.md](/rules/security-scale-ops.md)
Baseline security, kesiapan traffic sekitar 1000 user, queue/scheduler awareness, dan concern operasional.

### [database-performance.md](/rules/database-performance.md)
Aturan schema, indexing, query design, growth data, reporting/export, dan performa database.

## Workflow
### [project-init.md](/workflow/project-init.md)
Alur saat memulai project baru sebelum spesifikasi lengkap tersedia.

### [website-discovery.md](/workflow/website-discovery.md)
Panduan menggali kebutuhan website atau sistem dari nol.

### [module-planning.md](/workflow/module-planning.md)
Panduan memecah kebutuhan menjadi modul, dependency, dan fase implementasi.

### [feature-delivery.md](/workflow/feature-delivery.md)
Alur pengerjaan fitur dari intake scope sampai review sebelum selesai.

### [jobs-scheduling.md](/workflow/jobs-scheduling.md)
Panduan kapan memakai queue, job, scheduler, retry, timeout, dan monitoring task periodik.

## Cara Pakai Praktis
### Saat memulai project baru
Baca urutan ini:
1. `system_promt.md`
2. `workflow/website-discovery.md`
3. `workflow/project-init.md`
4. `workflow/module-planning.md`
5. rules yang relevan dengan stack yang dipilih

### Saat mulai implementasi fitur
Baca urutan ini:
1. `workflow/feature-delivery.md`
2. `rules/engineering-standards.md`
3. `rules/backend-api.md` atau `rules/ui-frontend.md`
4. `rules/testing-strategy.md`
5. `rules/security-scale-ops.md`
6. `rules/database-performance.md`

### Saat fitur melibatkan proses async
Tambahkan acuan:
1. `workflow/jobs-scheduling.md`
2. `rules/security-scale-ops.md`
3. `rules/testing-strategy.md`

## Prinsip Kerja Default Agent
- fokus pada maintainability, bukan sekadar cepat selesai
- utamakan Laravel, React, TypeScript, Golang, dan PHP modern
- CodeIgniter tidak jadi default untuk project baru
- jika React dipakai, default component basis adalah `shadcn/ui`
- jika Blade dipakai, utamakan custom Blade components sendiri
- jika frontend dan backend dipisah, default adalah `Next.js Pages Router` + `Auth.js` + `Go Fiber`
- security, testing, database, queue, dan operational readiness harus dipikirkan sejak awal
- session non-built-in harus tetap aman: cookie policy, expiry, invalidation, CSRF, dan boundary auth harus jelas
- HTTP method harus dipakai dengan benar: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`
- asumsi sekitar 1000 user harus tetap ditangani dengan desain query, pagination, indexing, cache, dan async processing yang wajar
- visual default cenderung clean, glossy blur, matte, cerah, dan menarik
- komponen umum lebih baik dijadikan reusable internal component agar page tinggal menyusun, bukan mengulang
- clean code, dry code, scalable structure, dan abstraction yang masuk akal lebih diutamakan daripada shortcut yang cepat kotor

## Catatan
- Folder ini adalah baseline umum, bukan spesifikasi project tertentu.
- Untuk setiap project baru, isi `.agents` ini bisa ditambah dokumen domain-specific sesuai kebutuhan website atau sistem yang sedang dibangun.
