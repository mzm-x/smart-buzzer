# Cara Import ke Google Sheets

1. Buka Google Sheets → buat spreadsheet baru kosong, kasih nama `TP Survey Plan`.
2. Import CSV **satu per satu, urut 01 → 10**:
   - File → Import → Upload → pilih CSV
   - Import location: **Insert new sheet(s)**
   - Separator type: **Comma** · Convert text to numbers/dates/formulas: **Yes** (wajib, biar rumus di 01-guide jalan)
3. **Rename tiap tab PERSIS** seperti ini (rumus di tab Guide refer ke nama ini):
   | File | Nama tab |
   |------|----------|
   | 01-guide.csv | `1. Guide` |
   | 02-timeline.csv | `2. Timeline` |
   | 03-segments.csv | `3. Segments` |
   | 04-lead-sourcing.csv | `4. Lead Sourcing` |
   | 05-script-outreach.csv | `5. Script Outreach` |
   | 06-pertanyaan-survey.csv | `6. Pertanyaan Survey` |
   | 07-lead-bank.csv | `7. Lead Bank` |
   | 08-response-log.csv | `8. Response Log` |
   | 09-analysis-rules.csv | `9. Analysis Rules` |
   | 10-pmsf-next.csv | `10. PMSF Next` |
4. Hapus sheet default `Sheet1`.
5. Format sekali jalan: Ctrl/Cmd+A di tiap tab → font **Arial 11**. Bold baris header manual (baris judul kolom). Freeze row header: View → Freeze → 1 row.
6. Dropdown (Data → Data validation):
   - `2. Timeline` kolom Status: `TODO, DOING, DONE`
   - `6. Pertanyaan Survey` kolom Keputusan Manager: `KEEP, CUT`
   - `7. Lead Bank` kolom R (Status): `New, Approached, Responded, Completed, Declined, No reply`
7. Baris contoh di `7. Lead Bank` & `8. Response Log` boleh dihapus setelah paham formatnya. Rumus LIVE COUNTER di `1. Guide` langsung hidup begitu nama tab 7 & 8 bener.
8. Format cell completion-rate & persen must-have di `1. Guide` (B24:B27): Format → Number → Percent.

Kalau mau XLS: setelah import selesai, File → Download → Microsoft Excel (.xlsx).
