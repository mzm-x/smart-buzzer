# TP Survey Plan — Tripadvisor Presence PMF Research (Smart Buzzer)

> **CARA PAKAI FILE INI:** Setiap heading `## ` di bawah = 1 tab di Google Sheets / Excel.
> Prompt buat convert file ini jadi XLS ada di bagian paling bawah (`## PROMPT — Convert ke XLS`).
> Status keputusan: Timeline start **13 Jul 2026** · Market **US tourist cities** · Eksekutor **solo** · Segment **S1, S2, S4** (40 leads/segment; S3/S5/S6 backlog) · Pertanyaan: **menunggu keputusan manager** (lihat kolom Verdict di tab Pertanyaan).

---

## 1. Guide

| Item | Isi |
|------|-----|
| Project | PMF Discovery — produk presence/reputasi Tripadvisor (Smart Buzzer) |
| Tujuan | Cari tahu WHO buyer-nya, real JOB yang mereka mau selesaikan (sering bukan "review"), cara mereka solve sekarang, dan willingness-to-pay |
| Metode | Survey online (21Q, ~5 min) + outbound manual — BUKAN ads |
| Goal | **20 completed surveys** (bukan 20 approach) |
| Survey URL | `https://smart-buzzer.com/tp-survey/?seg=S1&channel=whatsapp` (ganti seg & channel per lead) |
| Admin panel | `https://smart-buzzer.com/tp-survey/log.php` (6 tab: Overview / Analysis / Responses / Calls / Outbound / Playbook) |
| Export data | Admin → tab Responses → Export CSV / XLS |
| RULE #1 | SURVEY = NO UPSELL. Sekali pitch, tujuan riset hilang. Upsell cuma kalau MEREKA yang nanya |
| RULE #2 | GOAL = 20 completed, bukan 20 approach. Completion rate per segment = sinyal engagement |
| RULE #3 | JANGAN scale ke ads sebelum tahu siapa buyer sebenarnya. 1 sale dari ad ≠ channel works |

---

## 2. Timeline

> Start: Senin 13 Jul 2026. Kapasitas solo: ~30–40 approach/hari (personal, bukan blast massal).
> ⚠ Bentrok bandwidth: outreach clear-debt >120 days juga mulai 14 Jul (~15 klien/minggu via WATI) — jatah pagi buat TP survey, siang buat clear-debt.

| Periode | Fase | Aktivitas | Target | Status |
|---------|------|-----------|--------|--------|
| 6–12 Jul (prep) | W0 — Persiapan | Deploy `/tp-survey/` ke server · finalisasi pertanyaan sama manager · kumpulin leads TA-first (3 segment × 40 = ~120 leads) · test survey di HP sendiri | 120 leads siap + survey live | ☐ |
| 13–19 Jul | W1 — Test blast | Enrich leads (email/WA) · test blast 30 lead pertama (7–8 per segment) buat kalibrasi pesan · pantau drop-off per question di admin | 3–5 completed | ☐ |
| 20–26 Jul | W2 — Full blast | Blast sisa ~90 leads · follow-up #1 ke non-responder (48 jam setelah kirim) | 10 completed (kumulatif) | ☐ |
| 27 Jul–2 Aug | W3 — Follow-up & top-up | Follow-up #2 (terakhir) · top-up scrape di segment yang lagging · kalau seret: naikin incentive / ganti channel | 20 completed (kumulatif) | ☐ |
| 3–9 Aug | W4 — Analysis & calls | Baca tab Analysis + semua Q11/Q20 · pilih 1 (max 2) segment pemenang · mulai discovery call dari Call List | Keputusan segment + 5–10 calls | ☐ |

Matematika: completion rate cold outreach ~12–17% → 120 approach ≈ 15–20 completed. Kalau W2 baru 6–7, top-up 30–40 leads di W3.

---

## 3. Segments — Target Distribusi (SIAPA yang disebar)

> Keputusan: test **3 segment** dulu (S1, S2, S4). S3, S5, S6 = backlog (S6 = kandidat pertama batch 2 — paling TA-native).
> Kuota: 40 leads/segment → target 6–7 completed/segment.
> **⚠ FOKUS TRIPADVISOR:** filter rating × review pakai **angka TRIPADVISOR** (bubble rating + review count di listing TA), BUKAN angka Google Maps. Lead tanpa listing TA = skip (kecuali S2 yang memang ngetes batas wedge).

| ID | Test? | Bisnis | Lokasi | Rating × Review | Kenapa menarik | Kota target (US) | Kuota leads |
|----|-------|--------|--------|-----------------|----------------|------------------|-------------|
| S1 | ✅ YA | Restaurant | Tourist city | High (≥4.5 bubble) × Low (<100) | Disukai turis tapi under-reviewed — velocity play, WTP test paling bersih | Miami, Orlando, Las Vegas, New Orleans | 40 |
| S2 | ✅ YA | Restaurant | Non-tourist city | High (≥4.5 bubble) × Low (<100) | Disukai lokal, minim bukti — ngetes batas TA wedge vs Google | Charlotte, Columbus, Indianapolis, Kansas City | 40 |
| S3 | ☐ backlog | Restaurant | Tourist city | Low (≤4.0) × Decent (100–500) | Recovery + responding — job "protect sales" | — | — |
| S4 | ✅ YA | Hotel / B&B | Tourist city | Low (≤4.0) × Many (500+) | Reputasi mengakar, booking-dependent — presence upkeep (adjacent opp terbesar) | Orlando, Las Vegas, Myrtle Beach, San Diego | 40 |
| S5 | ☐ backlog | Hotel / B&B | Non-tourist city | High (≥4.5) × Decent (100–500) | Business-travel demand, maintain & edge out | — | — |
| S6 | ☐ backlog (batch 2 pertama) | Tour & activity | Tourist area | High (≥4.5) × Decent (100–500) | Rank ~ booking, urgency paling kuat — paling TA-native | — | — |

---

## 4. Cara Dapetin Responden (lead sourcing)

> **Prinsip: TRIPADVISOR = sumber kebenaran buat pilih lead. Google Maps scraper = cuma buat cari KONTAK (email/phone), karena TA nggak expose kontak.**

| # | Sumber | Cara | Output |
|---|--------|------|--------|
| 1 | **TripAdvisor top-list (PRIMARY)** | Buka TA per kota target: kategori Restaurants / Hotels / Things to Do. Sort/scan listing, catat yang match filter segment (bubble rating + review count TA). 15–20 menit/kota | Daftar bisnis per segment yang TA-nya beneran match |
| 2 | **Scraper /outbound/ (contact enrichment)** | Search nama bisnis hasil #1 di scraper (Apify Google Maps) buat ambil `email` + `phone` + socials. Alternatif cepat: scrape per kota lalu cross-match manual sama daftar TA | Email/WA per lead, biaya ~$2–5 total |
| 3 | Komunitas & koneksi | FB group restaurant/hotel owners US, asosiasi hospitality, existing client Smart Buzzer yang punya kenalan hospitality | Warm leads (completion rate lebih tinggi) |

**Mapping filter segment (angka TRIPADVISOR, bukan Google):**

| Segment | Kategori TA | Filter TA bubble rating | Filter TA review count | Kota |
|---------|-------------|------------------------|------------------------|------|
| S1 | Restaurants | ≥ 4.5 bubble | < 100 | Miami, Orlando, Las Vegas, New Orleans |
| S2 | Restaurants | ≥ 4.5 bubble (kalau ada TA) | < 100 | Charlotte, Columbus, Indianapolis, Kansas City |
| S4 | Hotels | ≤ 4.0 bubble | ≥ 500 | Orlando, Las Vegas, Myrtle Beach, San Diego |

Catatan: TA pakai skala bubble 0.5 (nggak ada 4.3) — ekuivalennya: "High" = ≥4.5, "Low" = ≤4.0. S2 (non-tourist) boleh listing TA tipis/kosong — justru itu yang ngetes di mana batas TA wedge berakhir.

**Channel per lead:** WhatsApp (WATI) → respon tercepat · Email → pakai alur outbound existing · Instagram DM → buat resto/tour yang aktif IG.
**Link tracking:** tiap lead dapet link `?seg=S1&channel=whatsapp` — segment & channel otomatis kecatet, nggak ada input manual.
**Incentive:** ringkasan hasil riset (benchmark bisnis sejenis) gratis sebagai thank-you. Buat Q21 call: gift card kecil ($5–10).
**⚠ WA safety:** jangan pakai nomor yang pernah kena ban. Pakai WATI / nomor central LP, max 30–40 pesan personal/hari, jangan blast template identik.

---

## 5. Script Outreach (English, no-pitch)

| # | Momen | Channel | Script |
|---|-------|---------|--------|
| 1 | First touch | WhatsApp | Hi [name]! I came across [business name] — congrats on the great rating. We're doing independent research on how local hospitality businesses manage their online presence & reviews (Tripadvisor, Google, etc.). No sales, just 21 short questions (~5 min): [link]. Happy to share the findings summary with you after — could be a useful benchmark! |
| 2 | First touch | Email | Subject: quick research question about [business name] — Body: Hi [name], we're researching how local hospitality businesses like [business name] manage their online presence & reviews. 100% research — no sales, no pitch. It's 21 short questions (~5 minutes): [link]. As a thank-you, we'll send you the findings summary so you can benchmark against similar businesses. Thanks so much! |
| 3 | First touch | IG DM | Hey! Love what you're doing at [business name] 👋 Quick one — we're running research on how hospitality businesses handle online reviews & presence. No sales, 5 min: [link]. We'll share the results summary as a thank-you! |
| 4 | Follow-up #1 (48 jam) | Sama dgn first touch | Hi [name], just floating this back up — the 5-min research survey closes soon and we'd genuinely value [business name]'s input: [link]. Thank you! |
| 5 | Follow-up #2 / terakhir (W3) | Sama | Last nudge, promise! 🙂 We're wrapping up the research this week. If you have 5 minutes, your answers would really help: [link]. Either way, thanks for your time! |

Aturan: sebut nama bisnis (personal > massal, response rate 2–3×) · kirim 08:00–10:00 waktu lokal bisnis · max 2× follow-up · jangan ubah intro copy survey (tone non-leading = data jujur).

---

## 6. Pertanyaan Survey (Q1–Q21, FULL — manager pilih final)

> **Verdict rekomendasi:** 🟢 WAJIB (axis segment / high-signal) · 🟡 KEEP kalau muat · 🔴 kandidat POTONG.
> **Rekomendasi tim:** Paket B = potong Q2, Q13, Q14, Q19 → 17 pertanyaan, ~3.5–4 menit.
> Kolom "Keputusan Manager" dikosongin — isi ✅ keep / ❌ cut.

| Sec | Q# | Pertanyaan (EN, customer-facing) | Tipe | Opsi jawaban | Signal | Verdict | Paket | Keputusan Manager |
|-----|----|-----------------------------------|------|--------------|--------|---------|-------|-------------------|
| A | Q1 | What type of business do you run? | radio + Other | Restaurant / Cafe / Hotel or B&B / Tour or activity operator / Bar / Other | Who | 🟢 WAJIB — mapping segment | A,B,C | |
| A | Q2 | How many locations do you operate? | radio | 1 / 2–4 / 5–15 / 15+ | Who (size) | 🔴 POTONG — cuma buat hipotesis W5 (multi-location) yang bukan prioritas | A | |
| A | Q3 | Is your business in a tourist-heavy area? | radio | Mostly tourists / Mixed / Mostly locals | Who (location) | 🟢 WAJIB — axis segment | A,B,C | |
| A | Q4 | What's your role? | radio + Other | Owner / Manager / Marketing / Other | Who (buyer) | 🟡 KEEP — tau siapa buyer, bukan penentu scoring | A,B | |
| A | Q5 | Roughly what's your current average rating? | radio | Under 3.5 / 3.5–4.2 / 4.3–4.6 / 4.7+ / Not sure | Who (rating axis) | 🟢 WAJIB — axis segment | A,B,C | |
| A | Q6 | Roughly how many reviews on your main platform? | radio | <20 / 20–100 / 100–500 / 500+ | Who (review axis) | 🟢 WAJIB — axis segment | A,B,C | |
| B | Q7 ⭐ | What's the #1 thing you're trying to GROW right now? | radio + Other | More new customers / More bookings / More repeat customers / Higher-value customers / Keep it steady / Other | Root goal | 🟢 WAJIB — inti riset | A,B,C | |
| B | Q8 ⭐ | How much do your customers rely on your online rating/reviews when choosing you? | radio | A lot / Somewhat / Not much / Not sure | Why (link) | 🟢 WAJIB — pain proxy scoring | A,B,C | |
| B | Q9 | Which platforms matter most to your business? (pick all) | checkbox | Google / Tripadvisor / Facebook / Yelp / Booking.com / Instagram / Other | Fit / wedge | 🟢 WAJIB — tes TA wedge vs Google (decision rule #4) | A,B,C | |
| B | Q10 | How important are Tripadvisor reviews to your revenue? | radio | Very / Somewhat / Not really / We're not on TA | TA relevance | 🟢 WAJIB — pasangan Q9 | A,B,C | |
| C | Q11 ⭐ | What's your single biggest challenge with your online PRESENCE right now? (not just reviews) | open text | — | Pain (open) | 🟢 WAJIB — emas #1, bahan pitch PMSF | A,B,C | |
| C | Q12 ⭐ | Besides getting reviews, which of these eat your time or worry you? (pick all) | checkbox | Replying to reviews / Keeping photos & info updated / Ranking vs competitors / Monitoring / Multiple platforms-locations / Bookings from listings / None | Adjacent needs | 🟢 WAJIB — produk berikutnya sembunyi di sini | A,B,C | |
| C | Q13 | When you get a negative review, what usually happens? | radio | We respond fast / We respond eventually / We don't respond / We often don't see it in time | Pain | 🟡→🔴 POTONG — cuma ngetes hipotesis W4 (risk protection) | A | |
| C | Q14 | What do you currently DO to get more reviews? (pick all) | checkbox | Ask in person / Signage or QR / Email / SMS / Nothing systematic / A tool or agency does it / Other | Behavior | 🟡→🔴 POTONG — nice-to-have, bukan penentu | A | |
| D | Q15 ⭐ | Do you use any tool or service for this today? | radio + detail | No / Free tools only / Paid tool (which?) / An agency (who?) | Budget | 🟢 WAJIB — budget exists = sinyal terkeras | A,B,C | |
| D | Q16 ⭐ | Have you ever PAID to improve reviews/reputation/online presence? | radio + amount | Yes → how much/month? / No / Considered it | Budget | 🟢 WAJIB — angka budget real | A,B,C | |
| E | Q17 ⭐ | If a tool could fix your biggest presence problem AND make collecting real reviews effortless — how valuable is that? | radio | Must-have / Nice-to-have / Not needed | Intent | 🟢 WAJIB — % must-have | A,B,C | |
| E | Q18 ⭐ | What would you expect to pay per month for that? | radio | <$30 / $30–75 / $75–150 / $150–300 / $300+ / Wouldn't pay | WTP | 🟢 WAJIB — penentu harga | A,B,C | |
| E | Q19 | At what monthly price would it be… (a) so cheap you'd doubt quality (b) a bargain (c) getting pricey but worth it (d) too expensive | number × 4 | — | WTP (advanced) | 🔴 POTONG — paling berat diisi, Q18 cukup buat 20 sampel | A | |
| E | Q20 ⭐ | Magic wand: if you could fix ONE thing about how customers find & choose you online, what would it be? | open text | — | Opportunity | 🟢 WAJIB — emas #2 | A,B,C | |
| F | Q21 ⭐ | Open to a quick 15-min call to tell us more? (No pitch — small thank-you for your time) | radio + contact | Yes → name+contact / Maybe later / No | Intent + warm list | 🟢 WAJIB — bahan bakar PMSF | A,B,C | |

**Paket:** A = Full 21 (~5 min) · **B = 17 (potong Q2/Q13/Q14/Q19, ~3.5–4 min) ← rekomendasi** · C = Core 14 (B minus Q4/Q9/Q10, ~2.5–3 min, kehilangan tes TA-wedge).
Catatan teknis: kalau manager potong pertanyaan, hapus entry-nya di array `Q` di `tp-survey/index.php` — 10 menit kerjaan.

---

## 7. Lead Bank (kolom template — paste hasil scrape ke sini)

| Kolom | Contoh |
|-------|--------|
| Lead# | 1 |
| Segment | S1 |
| Business name | Ocean Blue Grill |
| City | Miami |
| Category | Restaurant |
| TA URL | tripadvisor.com/Restaurant_Review-... |
| TA bubble rating | 4.5 |
| TA #reviews | 74 |
| Google rating (referensi) | 4.6 |
| Google #reviews (referensi) | 210 |
| Phone / WA | +1 305 xxx |
| Email | info@... |
| Instagram | @oceanblue |
| Website | oceanblue.com |
| Source | outbound-scraper / TA-manual / community |
| Assigned channel | whatsapp |
| Survey link | https://smart-buzzer.com/tp-survey/?seg=S1&channel=whatsapp |
| Status | New / Approached / Responded / Completed / Declined / No reply |
| Approach date | 2026-07-14 |
| Follow-up 1 | 2026-07-16 |
| Follow-up 2 | 2026-07-28 |
| Notes | — |

---

## 8. Response Log (kolom = persis export CSV admin, tinggal paste)

| # | Kolom |
|---|-------|
| 1 | Resp# |
| 2 | Date |
| 3 | Status |
| 4 | Seg |
| 5 | Channel |
| 6 | Time (s) |
| 7 | Q1 Biz type |
| 8 | Q2 Locations |
| 9 | Q3 Tourist? |
| 10 | Q4 Role |
| 11 | Q5 Rating |
| 12 | Q6 #Reviews |
| 13 | Q7 #1 Goal |
| 14 | Q8 Rely on reviews |
| 15 | Q9 Platforms |
| 16 | Q10 TA importance |
| 17 | Q11 Biggest challenge |
| 18 | Q12 Adjacent needs |
| 19 | Q13 Neg handling |
| 20 | Q14 Get reviews |
| 21 | Q15 Tool today |
| 22 | Q16 Ever paid |
| 23 | Q17 Value |
| 24 | Q18 Exp $/mo |
| 25 | Q19 Price sense |
| 26 | Q20 Magic wand |
| 27 | Q21 Call? |
| 28 | Contact name |
| 29 | Contact |

---

## 9. Analysis & Decision Rules

**Scoring segment (otomatis di admin panel, bobot dari framework):**

| Sinyal | Bobot | Sumber |
|--------|-------|--------|
| Pain intensity | 25% | Q8 "A lot" + kekuatan jawaban Q11 (judgment manusia) |
| Budget exists | 25% | Q15 paid tool/agency ATAU Q16 Yes |
| WTP vs target ($59–129) | 20% | Q18 ≥ $30–75 |
| Buying intent | 20% | Q17 Must-have + Q21 Yes |
| Engagement | 10% | Completion rate per segment (completed ÷ approached) |

**Decision rules (setelah ~20 data):**

| # | Rule |
|---|------|
| 1 | Segment "menang" kalau: pain tinggi + budget EXISTS + WTP ≥ target + cukup banyak mau call |
| 2 | Pilih 1 (max 2) segment buat unscalable selling. Jangan nyebar |
| 3 | Kalau NGGAK ada yang lolos setelah 20 data → jangan scale. Refine hipotesis, ulangi |
| 4 | Q9/Q10: kalau non-tourist bilang TA nggak penting tapi Google iya → pivot valid ke produk Google core |
| 5 | "Yes to call" (Q21) = warm list = bahan bakar PMSF |

**Watch-out mingguan:** cek drop-off per question di admin (Overview → in-progress "Qx drop"). Kalau banyak mati di soal yang sama → potong / pindahin soal itu.

---

## 10. PMSF Next (setelah segment menang)

| Fase | Aksi |
|------|------|
| 1. Lock ICP & offer | Tulis ICP 1 kalimat dari segment pemenang · bangun pesan dari KATA-KATA MEREKA (Q11/Q20) · set harga dari Q18/Q19 (anchor $59–129) · kalau adjacent need dominan, pertimbangkan lead pakai job ITU |
| 2. Sell unscalable | Mulai dari list "yes-to-call" (Q21) · target close 5–10 paying client 1:1 · log semua objection |
| 3. Gate PMSF | Repeatable close (bukan 1–2 deal hoki) + CAC < LTV + ICP cukup tajam buat di-target ads |
| 4. Scale ads | 1 channel, 1 segment, 1 message. Reinvest cuma kalau unit economics sehat |

---

## PROMPT — Convert ke XLS / Google Sheets (paste ke AI lain bareng file MD ini)

```
Gua punya file markdown rencana riset survey (tp-survey-plan.md, gua lampirkan).
Buatin file Excel (.xlsx) dari file itu — nanti gua upload ke Google Sheets.

ATURAN FORMAT (ketat):
1. Font: Arial 11 untuk SEMUA cell, termasuk header.
2. NO COLOR sama sekali — nggak ada background fill, nggak ada font warna.
   Header cukup bold + freeze row 1 + border bawah tipis.
3. NO EMOJI — hapus/ganti semua emoji jadi teks:
   🟢 -> "WAJIB", 🟡 -> "KEEP", 🔴 -> "POTONG", ⭐ -> "(high-signal)",
   ✅ -> "YA", ☐ -> "" (kosong), ⚠ -> "PENTING:", → -> "->".
4. Setiap heading "## " = 1 sheet/tab. Nama tab: "1. Guide", "2. Timeline",
   "3. Segments", "4. Lead Sourcing", "5. Script Outreach",
   "6. Pertanyaan Survey", "7. Lead Bank", "8. Response Log",
   "9. Analysis Rules", "10. PMSF Next".
   Bagian "PROMPT" ini JANGAN dijadiin tab.
5. Tabel markdown -> tabel di sheet: row 1 = header (bold, freeze).
   Teks di luar tabel jadi baris catatan biasa di atas/bawah tabel (italic boleh).
6. Wrap text untuk kolom panjang (pertanyaan, script, notes). Column width wajar.
7. Tab "6. Pertanyaan Survey": kolom "Keputusan Manager" kosong, kasih
   data validation dropdown: KEEP / CUT.
8. Tab "2. Timeline": kolom Status kasih dropdown: TODO / DOING / DONE.
9. Tab "7. Lead Bank" & "8. Response Log": header + 1 baris contoh saja,
   sisanya kosong (template kerja). Header Lead Bank urutannya:
   Lead# | Segment | Business name | City | Category | TA URL |
   TA bubble rating | TA #reviews | Google rating | Google #reviews |
   Phone/WA | Email | Instagram | Website | Source | Assigned channel |
   Survey link | Status | Approach date | Follow-up 1 | Follow-up 2 | Notes
   (kolom A sampai V). Status (kolom R) kasih dropdown:
   New / Approached / Responded / Completed / Declined / No reply.
10. Jangan ubah isi teks selain aturan emoji di atas.

RUMUS — masukin ke tab "1. Guide" sebagai blok "LIVE COUNTER" (label di
kolom A, rumus di kolom B), referensi ke tab lain:
   Completed total:
     =COUNTIF('8. Response Log'!C:C,"completed")
   Sisa ke goal 20:
     =20-COUNTIF('8. Response Log'!C:C,"completed")
   Completed per segment (contoh S1, duplikat buat S2/S4):
     =COUNTIFS('8. Response Log'!D:D,"S1",'8. Response Log'!C:C,"completed")
   Approached per segment (contoh S1, dari Lead Bank kolom R):
     =COUNTIFS('7. Lead Bank'!B:B,"S1",'7. Lead Bank'!R:R,"Approached")
       +COUNTIFS('7. Lead Bank'!B:B,"S1",'7. Lead Bank'!R:R,"Responded")
       +COUNTIFS('7. Lead Bank'!B:B,"S1",'7. Lead Bank'!R:R,"Completed")
       +COUNTIFS('7. Lead Bank'!B:B,"S1",'7. Lead Bank'!R:R,"Declined")
       +COUNTIFS('7. Lead Bank'!B:B,"S1",'7. Lead Bank'!R:R,"No reply")
   Completion rate S1 (ulangi per segment):
     =IFERROR(COUNTIFS('8. Response Log'!D:D,"S1",'8. Response Log'!C:C,"completed")/B_approached_S1,0)
     (ganti B_approached_S1 dengan cell approached S1 di atasnya)
   Persen Must-have (Q17 = kolom W di Response Log):
     =IFERROR(COUNTIF('8. Response Log'!W:W,"Must-have")/COUNTIF('8. Response Log'!C:C,"completed"),0)
   Yes-to-call (Q21 = kolom AA):
     =COUNTIF('8. Response Log'!AA:AA,"Yes")
   Format cell persen pakai format number percentage, bukan warna.

Output: 1 file .xlsx siap upload ke Google Sheets.
```
