# Journal Badges (OJS Plugin)

**Author:** Edi Suherlan · [audhighasu.com](https://audhighasu.com) · audhighasu@gmail.com  
**Version:** 1.0.0 · **License:** GPL-3.0

Cross-theme OJS plugin that shows an **accreditation & partners** strip (e.g. SINTA, DOAJ, Crossref, Garuda, partner logos) above the journal footer.

---

## English

### Features

- Works with **any frontend theme** (no theme hardcoding)
- Backend settings to edit badges (JSON)
- Dummy sample data included (SINTA 2, DOAJ, Crossref, Garuda, partner)
- Clickable badges open external URLs (new tab)
- Locales: English & Indonesian

### Minimum requirements

| Item | Minimum |
|------|---------|
| **OJS** | **3.5.0** or later |
| **PHP** | **8.1** or later (same as OJS 3.5) |
| **Extensions** | Standard OJS stack (PDO, mbstring, etc.) |
| **Access** | Journal Manager or Site Administrator |

> Built and tested on **OJS 3.5**. Not supported on OJS 3.3 / 3.4 without adaptation (different plugin APIs / namespaces).

### Installation

1. Download this repository as ZIP (or clone it).
2. Ensure the plugin folder is named **`journalBadges`**.
3. Upload / copy it to:

   ```text
   <ojs-root>/plugins/generic/journalBadges/
   ```

   Example (Laragon):

   ```text
   d:\laragon\www\ojs\plugins\generic\journalBadges\
   ```

4. Log in to OJS as **Journal Manager** or **Site Admin**.
5. Go to **Settings → Website → Plugins** (Generic Plugins).
6. Find **Journal Badges** → click **Enable**.
7. Open **Settings**, review/edit the badges, then **Save**.
8. Visit the public journal site and scroll above the footer — the strip should appear.

#### Optional (CLI)

From the OJS root (if you ship an enable script with your package):

```bash
php lib/pkp/tools/installPluginVersion.php plugins/generic/journalBadges/version.xml
```

Then enable the plugin in the UI and open its settings once to seed sample data.

### How to change badge data (e.g. SINTA 2 → SINTA 1)

1. **Settings → Website → Plugins → Journal Badges → Settings**
2. Edit **Badges (JSON)**. Example change:

```json
{
    "id": "sinta1",
    "label": "SINTA 1",
    "url": "https://sinta.kemdikbud.go.id/",
    "kind": "sinta",
    "note": "Sinta accreditation rank 1"
}
```

3. Save and hard-refresh the public site.

JSON object keys:

| Key | Description |
|-----|-------------|
| `id` | Internal id (letters/numbers) |
| `label` | Text shown on the badge |
| `url` | Link target (optional but recommended) |
| `kind` | Style: `sinta`, `doaj`, `crossref`, `garuda`, `partner`, `custom` |
| `note` | Tooltip / title text |

Tick **Restore sample badges** to reset to the built-in dummy set.

### Uninstall

1. Disable the plugin in **Plugins**.
2. Delete the folder `plugins/generic/journalBadges/`.

---

## Bahasa Indonesia

### Fitur

- Berjalan di **semua theme** frontend (tidak perlu hardcode di theme)
- Pengaturan di backend untuk mengubah lencana (JSON)
- Data contoh siap pakai (SINTA 2, DOAJ, Crossref, Garuda, partner)
- Lencana bisa diklik menuju URL eksternal
- Bahasa: Indonesia & Inggris

### Spesifikasi minimum

| Item | Minimum |
|------|---------|
| **OJS** | **3.5.0** atau lebih baru |
| **PHP** | **8.1** atau lebih baru (sesuai OJS 3.5) |
| **Ekstensi** | Stack standar OJS (PDO, mbstring, dll.) |
| **Akses** | Journal Manager atau Site Administrator |

> Dikembangkan & diuji pada **OJS 3.5**. Belum didukung di OJS 3.3 / 3.4 tanpa penyesuaian (API / namespace plugin berbeda).

### Cara instalasi

1. Unduh repositori ini sebagai ZIP (atau clone).
2. Pastikan nama foldernya **`journalBadges`**.
3. Unggah / salin ke:

   ```text
   <root-ojs>/plugins/generic/journalBadges/
   ```

   Contoh (Laragon):

   ```text
   d:\laragon\www\ojs\plugins\generic\journalBadges\
   ```

4. Login OJS sebagai **Journal Manager** atau **Site Admin**.
5. Buka **Settings → Website → Plugins** (Generic Plugins).
6. Cari **Journal Badges** / **Lencana Jurnal** → klik **Enable**.
7. Buka **Settings**, atur/edit lencana, lalu **Save**.
8. Buka situs publik jurnal, scroll ke atas footer — strip akreditasi harus muncul.

#### Opsional (CLI)

Dari root OJS:

```bash
php lib/pkp/tools/installPluginVersion.php plugins/generic/journalBadges/version.xml
```

Lalu aktifkan plugin di UI dan buka Settings sekali agar data contoh tersimpan.

### Cara ganti data (misalnya SINTA 2 → SINTA 1)

1. **Settings → Website → Plugins → Journal Badges → Settings**
2. Ubah **Badges (JSON)**. Contoh:

```json
{
    "id": "sinta1",
    "label": "SINTA 1",
    "url": "https://sinta.kemdikbud.go.id/",
    "kind": "sinta",
    "note": "Akreditasi Sinta Peringkat 1"
}
```

3. Simpan, lalu hard-refresh halaman publik.

Kunci objek JSON:

| Kunci | Keterangan |
|-------|------------|
| `id` | Id internal |
| `label` | Teks di lencana |
| `url` | Tautan tujuan |
| `kind` | Gaya: `sinta`, `doaj`, `crossref`, `garuda`, `partner`, `custom` |
| `note` | Teks tooltip |

Centang **Pulihkan lencana contoh** untuk mengembalikan data dummy bawaan.

### Mencopot instalasi

1. Nonaktifkan plugin di **Plugins**.
2. Hapus folder `plugins/generic/journalBadges/`.

---

## Support

- Website: [audhighasu.com](https://audhighasu.com)
- Email: audhighasu@gmail.com
- Repository: [github.com/edisuherlan/PluginOJS-JournalBadges](https://github.com/edisuherlan/PluginOJS-JournalBadges)
