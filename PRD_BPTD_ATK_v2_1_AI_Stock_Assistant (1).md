# PRD — BPTD Internal ATK Inventory & Distribution Management System
## With AI Stock Assistant

> **Version:** 2.1  
> **Status:** Development Ready  
> **Target:** BPTD Kelas II Jawa Timur  
> **Application Type:** Internal Web Application  
> **Backend:** Laravel  
> **Frontend:** Laravel Blade  
> **Database:** MySQL / PostgreSQL  
> **Primary User:** Superadmin / Petugas ATK  
> **AI Scope:** Read-only Stock Assistant  
> **PWA:** Not required  
> **Python:** Not required

---

# 1. Executive Summary

BPTD Internal ATK Inventory & Distribution Management System adalah aplikasi internal untuk mengelola persediaan Alat Tulis Kantor (ATK), distribusi ATK kepada pegawai/unit, penerimaan barang, pengadaan, monitoring stok, pelaporan, dan audit aktivitas.

Versi ini berfokus pada **Superadmin/Petugas ATK**.

Pegawai tidak melakukan request melalui aplikasi. Pegawai datang ke tempat ATK, kemudian petugas mencatat kebutuhan pegawai ke dalam sistem.

Alur operasional utama:

```text
Pegawai datang ke tempat ATK
        ↓
Petugas input data penerima
        ↓
Input ATK dan jumlah
        ↓
Validasi stok
        ↓
Simpan Stock Out
        ↓
Stok berkurang
        ↓
Stock Ledger tercatat
        ↓
Data tersedia untuk laporan
```

Selain fungsi inventory utama, sistem memiliki **AI Stock Assistant** yang hanya digunakan untuk membantu Superadmin memperoleh informasi stok dengan bahasa natural.

Contoh:

> "Berapa stok pulpen sekarang?"

AI mengambil data dari sistem dan menjawab kondisi stok.

AI **tidak diperbolehkan mengubah database, membuat transaksi, mengurangi stok, menambah stok, melakukan pengadaan, atau menghapus data.**

---

# 2. Product Goals

## 2.1 Tujuan Utama

Membangun sistem internal ATK yang:

- Terstruktur
- Akurat
- Mudah digunakan
- Mudah ditelusuri
- Memiliki pencatatan stok yang konsisten
- Mempermudah pekerjaan Petugas ATK
- Mempermudah pembuatan laporan
- Memiliki AI Assistant untuk pencarian informasi stok secara natural

## 2.2 Tujuan AI Assistant

AI Assistant hanya bertujuan untuk:

> **Mempermudah Superadmin mengecek informasi stok tanpa harus membuka banyak halaman atau melakukan filter manual.**

AI bukan pengganti sistem inventory.

Database tetap menjadi **source of truth**.

---

# 3. Scope

## 3.1 Included

### Core Inventory

- Authentication
- User management
- Dashboard
- Master ATK
- Kategori
- Satuan
- Unit kerja
- Data penerima
- Supplier
- Stock In
- Stock Out
- Stock Ledger
- Stock Adjustment
- Stock Opname
- Monitoring minimum stock
- Procurement
- Attachment/bukti
- Reports
- Import Excel
- Export Excel
- Audit Trail

### AI

- AI Stock Assistant
- Natural language stock query
- Stock availability
- Low stock query
- Out of stock query
- Item detail query
- Category stock query
- Stock summary
- Read-only response

---

# 4. Explicitly Out of Scope

Fitur berikut tidak dibangun pada fase ini:

- PWA
- Native Android/iOS
- Employee self-service request
- Employee dashboard
- Online request
- Approval request
- Central Office Alert
- Sound Reminder
- Push notification
- WhatsApp notification
- SMS
- Voice recognition
- AI transaction execution
- AI Stock In
- AI Stock Out
- AI Procurement
- AI database modification
- AI autonomous agent
- Python service

---

# 5. User Roles

## 5.1 Superadmin / Petugas ATK

Superadmin adalah pengguna utama.

Hak akses:

- Dashboard
- Master data
- Inventory
- Stock In
- Stock Out
- Stock Ledger
- Adjustment
- Stock Opname
- Procurement
- Reports
- Import/Export
- Audit Trail
- AI Stock Assistant
- User Management
- Settings

---

# 6. User/Penerima vs Application User

Sistem membedakan:

### Application User

Orang yang login ke sistem.

Contoh:

```text
Admin ATK
```

### ATK Recipient

Pegawai yang menerima ATK.

Recipient tidak perlu login.

Data minimal:

```text
Nama
NIP
Unit Kerja
```

Transaksi Stock Out harus menyimpan snapshot penerima agar histori tidak berubah apabila master penerima diubah di masa depan.

---

# 7. Core Distribution Workflow

```text
Recipient datang
      ↓
Petugas membuka Stock Out
      ↓
Input / pilih penerima
      ↓
Pilih ATK
      ↓
Masukkan jumlah
      ↓
Tambah item lain jika perlu
      ↓
Sistem validasi stok
      ↓
Konfirmasi
      ↓
Create Stock Out
      ↓
Decrease Current Stock
      ↓
Create Stock Ledger
      ↓
Create Audit Log
      ↓
Transaction Complete
```

---

# 8. Stock Out Transaction

## 8.1 Header

Field:

- Transaction Number
- Transaction Date
- Recipient Name
- NIP
- Unit
- Officer
- Notes

Nomor otomatis:

```text
OUT-20260923-0001
```

## 8.2 Details

Satu transaksi dapat memiliki banyak item.

Contoh:

```text
OUT-20260923-0001

Nama : Ahmad Fauzi
NIP  : 198xxxxxxxxx
Unit : Subbag Keuangan

Items:
- Pulpen       5 pcs
- Buku Tulis   3 pcs
- Map          10 pcs
```

---

# 9. Stock Validation

Stock Out harus memvalidasi:

```text
requested_quantity > 0
available_stock >= requested_quantity
item.status = active
```

Contoh:

```text
Stock tersedia : 5
Diminta        : 10

ERROR:
Stok tidak mencukupi.
```

Stock tidak boleh menjadi negatif.

---

# 10. Atomic Inventory Transaction

Stock Out harus menggunakan database transaction.

Concept:

```text
BEGIN TRANSACTION

Validate Stock

Create Stock Out
Create Stock Out Details
Decrease Current Stock
Create Stock Ledger
Create Audit Log

COMMIT
```

Jika gagal:

```text
ROLLBACK
```

Tidak boleh terjadi:

```text
Stock berkurang
tetapi ledger gagal
```

atau:

```text
Ledger tercatat
tetapi stock tidak berubah
```

---

# 11. Stock In

Stock In digunakan untuk menambah stok.

Sumber:

- Procurement
- Barang diterima
- Penambahan stok
- Adjustment
- Initial stock
- Sumber lain yang valid

Field:

- Transaction number
- Date
- Item
- Quantity
- Source
- Supplier
- Procurement reference
- Officer
- Notes
- Attachment

Contoh:

```text
IN-20260923-0001

Item     : Buku Tulis
Quantity : 100 pcs
Source   : Procurement
Supplier : PT ABC
```

---

# 12. Stock Ledger

Semua perubahan stok harus menghasilkan ledger.

Field minimal:

```text
item_id
transaction_type
quantity
balance_after
reference_type
reference_id
performed_by
created_at
```

Contoh:

```text
Date       Item      Type   Qty   Balance
------------------------------------------
23/09/26   Pulpen    IN     +100  100
23/09/26   Pulpen    OUT     -10   90
24/09/26   Pulpen    IN      +50  140
24/09/26   Pulpen    OUT      -5  135
```

Ledger menjadi histori resmi perubahan stok.

---

# 13. Stock Adjustment

Adjustment digunakan untuk koreksi stok yang memiliki alasan valid.

Field:

- Item
- System stock
- Actual stock
- Difference
- Reason
- Officer
- Date

Adjustment harus:

- Membuat ledger
- Membuat audit trail
- Menggunakan database transaction
- Memerlukan konfirmasi

---

# 14. Stock Opname

Stock Opname membandingkan:

```text
System Stock
vs
Physical Stock
```

Flow:

```text
Create Opname
     ↓
Select Items
     ↓
Input Physical Quantity
     ↓
Calculate Difference
     ↓
Review
     ↓
Confirm
     ↓
Adjustment
     ↓
Ledger
```

Tidak boleh mengubah stok hanya dengan input fisik tanpa proses konfirmasi.

---

# 15. Master ATK

Field:

```text
id
code
barcode
name
category_id
unit
minimum_stock
target_stock
current_stock
storage_location
supplier_id
status
description
created_at
updated_at
```

Status:

- Active
- Inactive

Item inactive tidak boleh digunakan untuk transaksi baru.

---

# 16. Stock Status

Status stok:

## AVAILABLE

```text
current_stock > minimum_stock
```

## LOW STOCK

```text
current_stock <= minimum_stock
AND current_stock > 0
```

## OUT OF STOCK

```text
current_stock = 0
```

Contoh:

```text
Pulpen
Current : 15
Minimum : 20

Status: LOW STOCK
```

---

# 17. Category

Kategori digunakan untuk grouping ATK.

Contoh:

- Alat Tulis
- Kertas
- Map & Folder
- Bahan Cetak
- Perlengkapan Meja
- Perlengkapan Arsip
- Lainnya

CRUD harus tersedia.

---

# 18. Unit of Measurement

Contoh:

- pcs
- box
- pack
- rim
- unit
- lusin

Satuan berasal dari master item dan digunakan secara konsisten pada transaksi.

---

# 19. Unit Kerja

Field:

```text
id
code
name
status
description
```

Digunakan untuk:

- Recipient
- Stock Out
- Reports

---

# 20. Recipient

Field:

```text
id
nip
name
unit_id
status
description
```

Recipient dapat dipilih saat Stock Out.

Transaksi tetap menyimpan snapshot:

```text
recipient_name
recipient_nip
recipient_unit_id
```

---

# 21. Supplier

Field:

```text
id
code
name
address
phone
email
contact_person
tax_number
status
notes
```

Digunakan pada Procurement dan Stock In.

---

# 22. Procurement

Procurement mencatat pengadaan ATK.

Header:

- Procurement Number
- Date
- Supplier
- Status
- Invoice Number
- Notes
- Total

Detail:

- Item
- Quantity
- Unit Price
- Subtotal

Formula:

```text
subtotal = quantity × unit_price
total = SUM(subtotal)
```

---

# 23. Procurement Status

Recommended:

```text
DRAFT
ORDERED
RECEIVED
COMPLETED
CANCELLED
```

Procurement yang belum diterima tidak menambah stock.

Saat barang diterima:

```text
Procurement
    ↓
Confirm Receipt
    ↓
Stock In
    ↓
Increase Stock
    ↓
Ledger
```

Sistem harus mencegah receipt ganda.

---

# 24. Dashboard

Dashboard menampilkan:

- Total item
- Total current stock
- Low stock count
- Out of stock count
- Stock Out today
- Stock In today
- Recent Stock Out
- Recent Stock In
- Low stock items
- Recent procurement
- Recent activity

Dashboard tidak menampilkan workflow request pegawai.

---

# 25. Reporting

Reports:

### Stock Report

- SKU
- Item
- Category
- Unit
- Current stock
- Minimum stock
- Target stock
- Status

### Stock In Report

Filter:

- Date range
- Item
- Supplier
- Source

### Stock Out Report

Filter:

- Date range
- Recipient
- NIP
- Unit
- Item
- Officer

### Unit Usage Report

Menampilkan konsumsi ATK per unit.

### Recipient Usage Report

Menampilkan histori ATK per penerima.

### Procurement Report

Menampilkan pengadaan berdasarkan periode/supplier/status.

---

# 26. Excel

## Import

Support:

- Items
- Recipients
- Units
- Suppliers
- Initial stock

Flow:

```text
Upload
 ↓
Validate
 ↓
Preview
 ↓
Show errors
 ↓
Confirm
 ↓
Import
```

## Export

Support:

- Stock
- Stock In
- Stock Out
- Stock Ledger
- Procurement
- Unit usage
- Recipient usage

Export mengikuti filter aktif.

---

# 27. Barcode

Barcode digunakan sebagai helper.

Use cases:

- Find item
- Open item detail
- Stock In
- Stock Out
- Stock Opname

Manual search tetap wajib tersedia.

---

# 28. Audit Trail

Aktivitas penting dicatat.

Field:

```text
user_id
action
module
record_type
record_id
old_values
new_values
ip_address
user_agent
created_at
```

Contoh:

```text
23 Sept 2026 08:15

Admin Ahmad

Stock Adjustment
Pulpen

Before: 100
After : 98

Reason:
Stock Opname
```

---

# 29. Delete Policy

Transaksi inventory tidak boleh di-hard-delete secara bebas.

Untuk transaksi yang salah gunakan:

- Cancel
- Void
- Reversal

sesuai business rule.

Tujuan:

> Histori stock harus tetap dapat ditelusuri.

---

# 30. AI STOCK ASSISTANT

## 30.1 Purpose

AI Stock Assistant adalah fitur bantuan bagi Superadmin untuk mencari informasi inventory menggunakan bahasa natural.

Contoh:

> "Berapa stok pulpen?"

> "Pulpen masih ada?"

> "Barang apa saja yang stoknya menipis?"

> "Ada berapa ATK yang habis?"

> "Berapa stok buku tulis?"

> "Tampilkan stok ATK kategori alat tulis."

AI harus membuat akses terhadap data stok menjadi lebih cepat.

---

# 31. AI Assistant — Strict Scope

AI hanya memiliki fungsi:

```text
READ
QUERY
SUMMARIZE
EXPLAIN
```

AI tidak memiliki fungsi:

```text
CREATE
UPDATE
DELETE
APPROVE
STOCK IN
STOCK OUT
ADJUSTMENT
PROCUREMENT
```

Dengan kata lain:

> **AI adalah read-only assistant.**

---

# 32. AI Source of Truth

AI tidak boleh mengarang jumlah stok.

Data harus berasal dari database aplikasi.

Flow:

```text
Superadmin
    ↓
Natural Language Question
    ↓
AI Assistant
    ↓
Intent Detection
    ↓
Inventory Query
    ↓
Laravel Application
    ↓
Database
    ↓
Structured Result
    ↓
AI Response
    ↓
Superadmin
```

AI tidak boleh langsung mengakses database dengan kredensial database.

AI harus meminta data melalui controlled application layer/tool.

---

# 33. Recommended AI Architecture

Karena Python tidak digunakan, AI diintegrasikan sebagai service dalam ekosistem Laravel.

```text
┌─────────────────────────────┐
│        Laravel Blade        │
│                             │
│   AI Stock Assistant UI     │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│     Laravel AI Service      │
│                             │
│ Intent / Tool Orchestration │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│ Controlled Inventory Tools  │
│                             │
│ getStock()                  │
│ searchItems()               │
│ getLowStock()               │
│ getOutOfStock()             │
│ getCategoryStock()          │
│ getStockHistory()           │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│       Laravel / Eloquent    │
│          Database           │
└─────────────────────────────┘
```

AI provider dapat dipilih pada implementasi.

PRD tidak mengunci vendor AI tertentu.

---

# 34. AI Tools

AI hanya boleh menggunakan tool/function yang telah ditentukan aplikasi.

Recommended tools:

## `search_items`

Mencari ATK berdasarkan:

- Name
- SKU
- Barcode
- Category

## `get_stock`

Mengambil:

- Item
- Current stock
- Unit
- Minimum stock
- Target stock
- Status

## `get_low_stock_items`

Mengambil semua item dengan:

```text
current_stock <= minimum_stock
AND current_stock > 0
```

## `get_out_of_stock_items`

Mengambil:

```text
current_stock = 0
```

## `get_category_stock`

Mengambil ringkasan stok berdasarkan kategori.

## `get_stock_history`

Mengambil histori perubahan stok untuk item tertentu.

AI tidak boleh diberi tool:

```text
create_stock_out
create_stock_in
update_stock
delete_stock
create_procurement
delete_item
```

---

# 35. AI Query Examples

## Query 1

User:

> "Berapa stok pulpen?"

System:

```text
Intent:
GET_STOCK

Entity:
Pulpen
```

Tool:

```text
get_stock("Pulpen")
```

Response:

```text
Pulpen saat ini tersedia 120 pcs.
Stok minimum: 30 pcs.
Status: Tersedia.
```

---

# 36. Query 2 — Low Stock

User:

> "Barang apa saja yang hampir habis?"

Intent:

```text
GET_LOW_STOCK_ITEMS
```

Response:

```text
Ada 4 item dengan stok menipis:

1. Buku Tulis — 15 pcs
2. Pensil — 12 pcs
3. Map — 18 pcs
4. Pulpen Biru — 20 pcs
```

---

# 37. Query 3 — Out of Stock

User:

> "Barang apa yang habis?"

Response:

```text
Ada 3 ATK yang stoknya habis:

1. Map Folio
2. Spidol Permanen
3. Kertas A4
```

---

# 38. Query 4 — Category

User:

> "Berapa total stok alat tulis?"

AI melakukan:

```text
get_category_stock("Alat Tulis")
```

Response berdasarkan data aktual.

---

# 39. Query 5 — Ambiguous Item

User:

> "Berapa stok buku?"

Jika database memiliki:

```text
Buku Tulis
Buku Agenda
Buku Ekspedisi
```

AI tidak boleh memilih secara sembarangan.

Response:

```text
Saya menemukan beberapa item:

1. Buku Tulis
2. Buku Agenda
3. Buku Ekspedisi

Silakan pilih item yang dimaksud.
```

---

# 40. AI Hallucination Prevention

AI wajib mengikuti aturan:

1. Jangan membuat angka stok.
2. Jangan menebak item.
3. Jangan mengarang supplier.
4. Jangan mengarang transaksi.
5. Jangan mengklaim data jika query database gagal.
6. Jika data tidak ditemukan, katakan tidak ditemukan.
7. Jika pertanyaan ambigu, minta klarifikasi.
8. Gunakan data terbaru yang dikembalikan application layer.

Jika database tidak tersedia:

```text
Maaf, data stok saat ini tidak dapat diakses.
Silakan coba kembali.
```

Bukan memberikan jawaban berdasarkan asumsi.

---

# 41. AI Response Format

Response ideal:

```text
📦 Pulpen

Stok saat ini : 120 pcs
Stok minimum  : 30 pcs
Target stok   : 100 pcs
Status        : Tersedia

Terakhir diperbarui:
23 September 2026, 08:15
```

Untuk multiple items:

```text
📦 Stok Menipis

1. Buku Tulis — 15 pcs
2. Pensil — 12 pcs
3. Map — 18 pcs

Total: 3 item
```

---

# 42. AI UI

AI Assistant dapat ditempatkan sebagai:

- Floating assistant
- Sidebar assistant
- Dedicated assistant page
- Dashboard widget

Visual final mengikuti UI/UX reference yang akan diberikan kemudian.

Contoh konsep:

```text
┌──────────────────────────────────────┐
│ AI Stock Assistant                   │
├──────────────────────────────────────┤
│                                      │
│ Anda:                                │
│ "Berapa stok pulpen?"                │
│                                      │
│ AI:                                  │
│ Pulpen tersedia 120 pcs.             │
│ Minimum 30 pcs. Status tersedia.     │
│                                      │
├──────────────────────────────────────┤
│ Tanya stok...                [Send]   │
└──────────────────────────────────────┘
```

---

# 43. AI Conversation Context

AI dapat mempertahankan context percakapan pendek.

Contoh:

```text
User:
Berapa stok pulpen?

AI:
120 pcs.

User:
Kalau buku tulis?

AI:
15 pcs.
```

Context hanya digunakan untuk memahami referensi percakapan.

Context tidak boleh menjadi source of truth.

Setiap angka harus berasal dari query inventory terbaru.

---

# 44. AI Security

AI harus mengikuti permission user yang sedang login.

Jika user tidak memiliki permission melihat inventory:

```text
AI tidak boleh mengambil data.
```

Semua AI request harus memiliki:

- Authenticated user
- User ID
- Permission context
- Request ID
- Timestamp

Audit AI dapat mencatat:

```text
user_id
question
tool_used
query_result_summary
timestamp
```

Jangan menyimpan data sensitif lebih lama dari kebutuhan.

---

# 45. AI Rate Limit

Untuk mencegah abuse:

- Rate limit AI requests
- Timeout
- Maximum prompt length
- Maximum response length
- Maximum tool calls per request

Jika limit tercapai:

```text
Permintaan AI terlalu banyak.
Silakan coba beberapa saat lagi.
```

---

# 46. AI Error Handling

Jika AI provider gagal:

```text
AI Assistant sedang tidak tersedia.
```

Namun seluruh inventory system tetap harus berjalan normal.

AI adalah:

> **Optional convenience layer, bukan core dependency.**

Jika AI mati:

- Stock In tetap berjalan.
- Stock Out tetap berjalan.
- Stock Ledger tetap berjalan.
- Reports tetap berjalan.
- Dashboard tetap berjalan.

---

# 47. AI Cost Control

Karena pertanyaan stock biasanya sederhana, application layer harus melakukan deterministic lookup terlebih dahulu jika memungkinkan.

Contoh:

```text
"Berapa stok pulpen?"
```

Tidak perlu meminta AI melakukan kalkulasi sendiri.

AI digunakan untuk:

```text
Natural Language
→ Intent
→ Tool
→ Database
→ Natural Language Response
```

Database/application tetap melakukan:

- Query
- Calculation
- Aggregation
- Filtering

AI hanya membantu interpretasi dan penyajian.

---

# 48. AI Must Not Execute Transactions

Sangat penting:

AI tidak boleh menjalankan:

```text
"Kurangi stok pulpen 10."
```

AI harus menolak sebagai action.

Response:

```text
AI Stock Assistant hanya dapat membaca informasi stok.
Untuk mengubah stok, gunakan menu Stock Out.
```

Hal yang sama untuk:

> "Tambahkan 100 buku."

Response:

```text
AI Stock Assistant tidak dapat melakukan Stock In.
Silakan gunakan menu Stock In.
```

---

# 49. AI Query Intent

Minimal intent:

```text
GET_ITEM_STOCK
SEARCH_ITEM
GET_LOW_STOCK
GET_OUT_OF_STOCK
GET_CATEGORY_STOCK
GET_STOCK_HISTORY
GET_STOCK_SUMMARY
UNKNOWN
```

---

# 50. AI Out-of-Scope Intent

Intent berikut harus ditolak/redirect:

```text
CREATE_STOCK_IN
CREATE_STOCK_OUT
UPDATE_STOCK
DELETE_ITEM
CREATE_PROCUREMENT
UPDATE_PROCUREMENT
DELETE_TRANSACTION
APPROVE_TRANSACTION
```

---

# 51. AI Testing

Minimal test:

### Test 1

Input:

```text
Berapa stok pulpen?
```

Expected:

- Item resolved
- Database queried
- Correct stock returned

### Test 2

Input:

```text
Barang apa yang habis?
```

Expected:

- Out of stock query
- Correct list

### Test 3

Input:

```text
Kurangi stok pulpen 5.
```

Expected:

- No database modification
- Explain Stock Out must be done through transaction module

### Test 4

Input:

```text
Tambahkan 100 buku.
```

Expected:

- No Stock In
- No database modification

### Test 5

Input:

```text
Berapa stok buku?
```

If ambiguous:

- Ask clarification

---

# 52. AI Audit

AI usage should be auditable.

Example:

```text
AI Request

User:
Admin Ahmad

Question:
"Barang apa saja yang stoknya menipis?"

Tools:
get_low_stock_items

Result:
4 items

Timestamp:
23/09/2026 08:30
```

AI audit should not expose internal API keys or sensitive system credentials.

---

# 53. AI and Database Architecture Rule

AI must never have unrestricted database access.

Bad:

```text
AI
 ↓
Direct SQL
 ↓
Database
```

Required:

```text
AI
 ↓
Allowed Tool
 ↓
Laravel Service
 ↓
Eloquent / Query Builder
 ↓
Database
```

This provides control over:

- Permissions
- Query scope
- Data exposure
- Logging
- Validation

---

# 54. Laravel Architecture

Recommended:

```text
app/

Models/
Services/
Repositories/
Http/Controllers/
Http/Requests/
Policies/
Actions/
AI/
    StockAssistantService.php
    Tools/
        SearchItemsTool.php
        GetStockTool.php
        GetLowStockTool.php
        GetOutOfStockTool.php
        GetCategoryStockTool.php
        GetStockHistoryTool.php
```

Exact structure may be adjusted to project conventions.

---

# 55. AI Service

Conceptual:

```text
StockAssistantService

receiveQuestion()
      ↓
validateUser()
      ↓
detectIntent()
      ↓
selectAllowedTool()
      ↓
executeTool()
      ↓
receiveStructuredResult()
      ↓
generateResponse()
      ↓
returnResponse()
```

AI service must not contain direct stock mutation logic.

---

# 56. Core Service Layer

Recommended services:

```text
StockInService
StockOutService
StockAdjustmentService
StockOpnameService
ProcurementService
StockLedgerService
ReportService
ImportService
ExportService
StockAssistantService
```

---

# 57. Database Entities

Core:

```text
users
roles
permissions

items
categories
units
recipients
work_units
suppliers

stock_ins
stock_in_details

stock_outs
stock_out_details

stock_adjustments
stock_adjustment_details

stock_opnames
stock_opname_details

stock_ledgers

procurements
procurement_details

attachments
audit_logs
ai_logs
settings
```

---

# 58. Stock Calculation

Concept:

```text
Current Stock
=
Initial Stock
+ Stock In
- Stock Out
+/- Adjustment
```

`current_stock` dapat disimpan untuk operational performance.

Ledger tetap menjadi histori perubahan.

---

# 59. Transaction Numbering

Examples:

```text
IN-20260923-0001
OUT-20260923-0001
ADJ-20260923-0001
OPN-20260923-0001
PO-20260923-0001
```

Nomor harus unique.

---

# 60. Security

Minimum:

- CSRF protection
- Authentication
- Authorization
- Password hashing
- Session security
- Form validation
- Mass assignment protection
- Query protection
- File validation
- Audit trail
- AI permission checks
- AI rate limiting
- Database backup
- Private attachment access

---

# 61. Performance

- Pagination
- Eager loading
- Database indexing
- Avoid N+1
- Server-side filtering
- Aggregate queries
- Queue large exports if needed
- AI timeout
- AI rate limit
- Cache suitable read-only stock summaries if necessary

---

# 62. Testing Strategy

## Unit Tests

- Stock calculation
- Stock status
- Minimum stock detection
- Procurement total
- Transaction number
- AI intent handling

## Feature Tests

- Login
- Master ATK
- Stock In
- Stock Out
- Adjustment
- Opname
- Procurement
- Reports
- Import
- Export
- Audit
- AI Stock Assistant

## AI Tests

- Correct item resolution
- Correct stock response
- Ambiguous query
- Out-of-stock query
- Low-stock query
- Unauthorized query
- Mutation rejection
- AI provider failure

---

# 63. Critical Inventory Test

Given:

```text
Initial = 100
Stock Out = 10
```

Expected:

```text
Current = 90
Ledger = -10
Stock Out = created
Audit = created
```

If any step fails:

```text
Rollback all
```

---

# 64. Critical AI Safety Test

Input:

```text
Kurangi stok pulpen 10.
```

Expected:

```text
No mutation
No Stock Out
No Ledger change
No database update
```

Response:

```text
AI Stock Assistant hanya dapat mengecek informasi stok.
Gunakan menu Stock Out untuk mencatat pengeluaran ATK.
```

---

# 65. UI/UX Placeholder

UI/UX visual akan diberikan sebagai referensi tambahan.

PRD ini menjadi source of truth untuk:

- Business logic
- Data
- Workflow
- Permission
- Inventory rules
- AI limitations

UI/UX reference menjadi source of truth untuk:

- Layout
- Visual hierarchy
- Components
- Colors
- Typography
- Spacing
- Interaction patterns
- Responsive behavior

UI tidak boleh mengubah business rules.

---

# 66. Navigation

Recommended:

```text
Dashboard

Inventory
├── Master ATK
├── Categories
├── Stock In
├── Stock Out
├── Stock Ledger
├── Stock Opname
└── Adjustment

Master Data
├── Work Units
├── Recipients
└── Suppliers

Procurement
├── Procurement
└── Attachments

Reports
├── Stock
├── Stock In
├── Stock Out
├── By Unit
├── By Recipient
└── Procurement

AI Assistant

System
├── Users
├── Roles & Permissions
├── Audit Trail
└── Settings
```

---

# 67. AI Assistant Navigation

AI Assistant dapat:

- berada pada sidebar navigation,
- tersedia sebagai floating button,
- atau menjadi widget dashboard.

Pilihan akhir mengikuti UI/UX reference.

AI Assistant tidak boleh menggantikan halaman inventory.

---

# 68. Development Rules for Claude

Claude harus mengikuti aturan berikut.

## Rule 1

Gunakan Laravel + Blade.

## Rule 2

Tidak menggunakan Python.

## Rule 3

Tidak membuat PWA sebagai requirement.

## Rule 4

Tidak membuat employee self-service request.

## Rule 5

Tidak membuat central alert atau sound reminder.

## Rule 6

AI hanya untuk membaca informasi stok.

## Rule 7

AI tidak boleh mengubah database.

## Rule 8

Semua stock mutation melalui service layer.

## Rule 9

Semua stock mutation menggunakan database transaction.

## Rule 10

Setiap stock mutation menghasilkan ledger.

## Rule 11

Stock tidak boleh negatif.

## Rule 12

Transaksi tidak boleh di-hard-delete sembarangan.

## Rule 13

Gunakan Form Request.

## Rule 14

Gunakan Policies/Permissions.

## Rule 15

Gunakan reusable Blade components.

## Rule 16

Jangan membuat mock logic sebagai pengganti business logic.

## Rule 17

AI harus menggunakan controlled tools.

## Rule 18

AI tidak boleh memiliki unrestricted database access.

## Rule 19

Jika AI tidak yakin, jangan mengarang.

## Rule 20

Jika query ambigu, minta klarifikasi.

---

# 69. Development Sequence

```text
1. Laravel Setup
        ↓
2. Authentication
        ↓
3. Database Migrations
        ↓
4. Models & Relationships
        ↓
5. Roles & Permissions
        ↓
6. Master Data
        ↓
7. Inventory Engine
        ↓
8. Stock In
        ↓
9. Stock Out
        ↓
10. Stock Ledger
        ↓
11. Adjustment
        ↓
12. Stock Opname
        ↓
13. Dashboard
        ↓
14. Procurement
        ↓
15. Reports
        ↓
16. Import / Export
        ↓
17. Audit Trail
        ↓
18. AI Stock Assistant
        ↓
19. Testing
        ↓
20. UI/UX Refinement
        ↓
21. Production Hardening
```

AI dibuat setelah inventory engine stabil karena AI harus membaca data inventory yang sudah benar.

---

# 70. Success Criteria

## Inventory

- Stock In bekerja.
- Stock Out bekerja.
- Stock tidak negatif.
- Ledger selalu tercatat.
- Adjustment dapat ditelusuri.
- Stock Opname dapat dilakukan.

## Distribution

- Petugas dapat mencatat nama.
- Petugas dapat mencatat NIP.
- Petugas dapat memilih unit.
- Petugas dapat memasukkan banyak item.
- Pengeluaran mengurangi stok secara otomatis.

## Reporting

- Stock report.
- Stock In report.
- Stock Out report.
- Unit usage report.
- Recipient usage report.
- Procurement report.
- Excel export.

## AI

- Superadmin dapat bertanya tentang stok.
- AI mengambil data aktual.
- AI dapat mencari item.
- AI dapat menampilkan low stock.
- AI dapat menampilkan out of stock.
- AI dapat menampilkan category stock.
- AI dapat menjelaskan status stok.
- AI tidak dapat mengubah data.
- AI tidak mengarang angka.
- AI tetap mengikuti permission user.

---

# 71. Final System Flow

```text
                    SUPERADMIN
                        │
                        ▼
                 ┌──────────────┐
                 │   Dashboard  │
                 └──────┬───────┘
                        │
       ┌────────────────┼────────────────┐
       │                │                │
       ▼                ▼                ▼
   MASTER DATA       INVENTORY        PROCUREMENT
       │                │                │
       │        ┌───────┴───────┐        │
       │        │               │        │
       │        ▼               ▼        ▼
       │    STOCK IN        STOCK OUT  RECEIVING
       │        │               │        │
       │        └───────┬───────┘        │
       │                ▼                │
       │          STOCK LEDGER            │
       │                │                │
       │                ▼                │
       │          STOCK MONITORING ◄─────┘
       │                │
       │         ┌──────┴──────┐
       │         ▼             ▼
       │      LOW STOCK    OUT OF STOCK
       │
       ▼
    REPORTING

                        +
                        │
                        ▼
              ┌───────────────────┐
              │ AI STOCK ASSISTANT│
              └─────────┬─────────┘
                        │
                  Read-only query
                        │
                        ▼
               Controlled Tool Layer
                        │
                        ▼
                    Laravel
                        │
                        ▼
                    Database
```

---

# 72. Product Principle

> **Simple → Structured → Accurate → Traceable → Scalable**

### Simple
Petugas dapat mencatat distribusi ATK dengan cepat.

### Structured
Master data dan transaksi terorganisasi.

### Accurate
Stok berasal dari transaksi yang tervalidasi.

### Traceable
Setiap perubahan stok dapat ditelusuri.

### Scalable
Sistem dapat dikembangkan menjadi BPTD Internal Management System.

### AI-Assisted
AI membantu Superadmin membaca informasi stok dengan bahasa natural, tanpa mengambil alih kontrol inventory.

---

# 73. Final Scope Statement

Sistem ini adalah:

> **Internal ATK Inventory & Distribution Management System berbasis Laravel + Blade untuk Superadmin/Petugas ATK, dengan AI Stock Assistant read-only sebagai alat bantu pengecekan stok.**

Workflow utama:

```text
User datang
    ↓
Petugas input:
Nama
NIP
Unit
ATK
Jumlah
    ↓
Stock Out
    ↓
Stock berkurang
    ↓
Stock Ledger
    ↓
Monitoring
    ↓
Reporting
```

Replenishment:

```text
Stock menipis
    ↓
Procurement
    ↓
Barang diterima
    ↓
Stock In
    ↓
Stock bertambah
    ↓
Stock Ledger
```

AI:

```text
Superadmin bertanya
        ↓
AI memahami pertanyaan
        ↓
Controlled Inventory Tool
        ↓
Laravel
        ↓
Database
        ↓
Data aktual
        ↓
AI menjelaskan hasil
```

AI hanya membaca.

**AI tidak pernah menjadi pihak yang mengubah stok atau database.**

