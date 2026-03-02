# 📦 Dokumentasi Sistem Rekomendasi Stok AI

> Referensi lengkap cara kerja fitur rekomendasi restock berbasis AI untuk developer baru.

---

## Arsitektur Keseluruhan

```mermaid
flowchart LR
    A["🖥️ Frontend\nform.js"] -->|AJAX GET| B["🔌 Controller\nRecommendationController"]
    B -->|call| C["⚙️ Service\nRecommendationService"]
    C -->|query| D["🗄️ Repository\nRecommendationRepository"]
    C -->|prompt| E["🤖 AI Agent\nStockRecommendationAgent"]
    
    C -->|"1. Pre-compute"| F["📊 Metrics"]
    C -->|"2. Compute Qty"| G["🧮 PHP Math"]
    C -->|"3. Descriptions"| E
    
    G -->|recommendations| A
    E -->|descriptions| A
```

---

## Alur Utama `generateAIRecommendation()`

```mermaid
flowchart TD
    START(["▶ Button AI Diklik"]) --> FETCH["1. Ambil semua produk\ndari database"]
    FETCH --> PRECOMPUTE["2. Hitung metrik per produk\ndays_of_stock, reorder_point,\nprofit_margin, suggested_qty,\nurgency, priority_score"]
    PRECOMPUTE --> COMPUTE["3. computeOptimalQuantities()\nHitung qty final sesuai budget"]
    COMPUTE --> AIDESC["4. enrichWithAIDescriptions()\nAI tulis deskripsi singkat"]
    AIDESC --> FALLBACK{"AI berhasil?"}
    FALLBACK -->|Ya| MERGE["Gabungkan deskripsi AI"]
    FALLBACK -->|Tidak| GENFALLBACK["generateFallbackDescription()\nDeskripsi otomatis dari data"]
    MERGE --> RETURN["5. Return JSON ke frontend"]
    GENFALLBACK --> RETURN
    RETURN --> FRONTEND["Frontend update input qty\n+ tampilkan deskripsi\n+ hitung Grand Total"]
```

---

## Detail Algoritma `computeOptimalQuantities()`

```mermaid
flowchart TD
    INPUT["Input: items[], budget"] --> CALC_TOTAL["Hitung total biaya\nSUM(suggested_qty × purchase_price)"]
    CALC_TOTAL --> CHECK{"totalCost vs budget?"}
    
    CHECK -->|"totalCost = 0\n(semua suggested_qty = 0)"| DISTRIBUTE["Distribusi budget\nberdasarkan priority_score"]
    CHECK -->|"totalCost ≤ budget\n(under budget)"| SCALE_UP["Scale UP\nqty × (1 + (scale-1) × priorityRatio)"]
    CHECK -->|"totalCost > budget\n(over budget)"| SCALE_DOWN["Scale DOWN"]
    
    SCALE_DOWN --> PROTECT{"Urgency\nCRITICAL/HIGH?"}
    PROTECT -->|Ya| GENTLE["Scale ringan\nfactor = max(scale, 0.7)"]
    PROTECT -->|Tidak| AGGRESSIVE["Scale agresif\nfloor(qty × scale)"]
    
    DISTRIBUTE --> CAP
    SCALE_UP --> CAP
    GENTLE --> CAP
    AGGRESSIVE --> CAP
    
    CAP["🛑 Cap: Max Days Coverage\nfast=30d, medium=21d,\nslow=14d, dead=0"]
    CAP --> DEAD{"Status = dead?"}
    DEAD -->|Ya| ZERO["qty = 0"]
    DEAD -->|Tidak| MOQ["Round ke MOQ\nround(qty / moq) × moq"]
    ZERO --> BUDGET_CAP
    MOQ --> BUDGET_CAP
    
    BUDGET_CAP{"Total > budget?"}
    BUDGET_CAP -->|Ya| FINAL_SCALE["Proportional scale down\n+ re-round MOQ"]
    BUDGET_CAP -->|Tidak| DONE["✅ Return recommendations"]
    FINAL_SCALE --> DONE
```

---

## Formula Referensi

| Metrik | Formula | Fungsi |
|---|---|---|
| `days_of_stock` | `current_stock / avg_daily_sales` | Berapa hari stok bertahan |
| `reorder_point` | `ceil(avg_daily × lead_time)` | Kapan harus pesan |
| `safety_stock` | `ceil(avg_daily × lead_time)` | Buffer minimum |
| `suggested_qty` | `max(ROP + safety_stock - current_stock, 0)` → MOQ | Baseline reorder |
| `profit_margin` | `(sell_price - purchase_price) / sell_price` | Profitabilitas |
| `priority_score` | `(urgencyW × 2) + statusW + (margin × 5) + (score × 3)` | Bobot prioritas keseluruhan |

### Urgency Levels

| Level | Kondisi | Weight |
|---|---|---|
| 🔴 CRITICAL | Stok < 3 hari | 4 |
| 🟠 HIGH | Stok < 7 hari | 3 |
| 🟡 MEDIUM | Stok < 14 hari | 2 |
| 🟢 LOW | Stok ≥ 14 hari | 1 |

### Max Days Coverage (Anti Over-Order)

| Status | Max Hari | Contoh (laku 10/hari) |
|---|---|---|
| Fast | 30 hari | max 300 unit |
| Medium | 21 hari | max 210 unit |
| Slow | 14 hari | max 140 unit |
| Dead | 0 hari | 0 unit |

---

## Tanggung Jawab Tiap Method

```mermaid
flowchart LR
    subgraph Service["RecommendationService"]
        A["generateAIRecommendation()"] -->|orchestrator| B["computeOptimalQuantities()"]
        A --> C["enrichWithAIDescriptions()"]
        C --> D["generateFallbackDescription()"]
    end
    
    subgraph Deskripsi
        B -.- B1["Semua math: ROP,\nscaling, MOQ, budget cap"]
        C -.- C1["Kirim data ke AI,\nhanya minta deskripsi"]
        D -.- D1["Fallback jika AI gagal,\npakai data angka"]
    end
```

| Method | File | Tugas |
|---|---|---|
| `generateAIRecommendation()` | `RecommendationService.php` | Orchestrator: pre-compute → qty → deskripsi |
| `computeOptimalQuantities()` | `RecommendationService.php` | Hitung qty (scaling, MOQ, budget cap) |
| `enrichWithAIDescriptions()` | `RecommendationService.php` | Panggil AI untuk deskripsi bahasa Indonesia |
| `generateFallbackDescription()` | `RecommendationService.php` | Deskripsi otomatis jika AI gagal |
| `getAiRecommendations()` | `RecommendationController.php` | API endpoint `/ai/{id}` |
| `applyAIToVisibleRows()` | `form.js` | Update input qty + inject deskripsi ke DOM |
| `calculateGrandTotal()` | `form.js` | Hitung total nominal semua halaman |

---

## Data Flow: Frontend ↔ Backend

```mermaid
sequenceDiagram
    participant U as 👤 User
    participant F as 🖥️ form.js
    participant C as 🔌 Controller
    participant S as ⚙️ Service
    participant AI as 🤖 AI Agent
    
    U->>F: Klik "Hasilkan Rekomendasi AI"
    F->>F: Show loading spinner
    F->>C: GET /ai/{historyId}
    C->>S: generateAIRecommendation(id)
    
    Note over S: 1. Fetch products
    Note over S: 2. Pre-compute metrics
    Note over S: 3. computeOptimalQuantities()
    
    S->>AI: Prompt (data + minta deskripsi)
    AI-->>S: JSON [{id, ai_description}]
    
    S-->>C: {recommendations, products}
    C-->>F: JSON Response
    
    F->>F: savedQuantities = AI qty
    F->>F: savedDescriptions = AI desc
    F->>F: Update visible inputs
    F->>F: Inject deskripsi di bawah nominal
    F->>F: calculateGrandTotal()
    F->>U: Toast "Berhasil"
```

---

## File Map

```
app/
├── Services/Report/
│   └── RecommendationService.php     ← Core logic (4 methods)
├── Http/Controllers/Report/
│   └── RecommendationController.php  ← API endpoint
├── Repositories/Report/
│   └── RecommendationRepository.php  ← Database queries
└── Agents/
    └── StockRecommendationAgent.php  ← AI agent config

resources/
├── js/pages/report/recommendation/
│   └── form.js                       ← Frontend handler
├── css/pages/report/recommendation/
│   └── form.css                      ← Styling (AI button, descriptions)
└── views/report/recommendation/
    └── form.blade.php                ← HTML template

database/
├── migrations/
│   └── *_create_stock_moving_details_table.php
└── seeders/
    └── RecommendationStockDetailSeeder.php
```

---

## Jaminan Sistem

| Jaminan | Cara Kerja |
|---|---|
| ✅ Total ≤ COGS Balance | PHP `computeOptimalQuantities()` + final budget cap |
| ✅ Qty = kelipatan MOQ | PHP `round(qty/moq) × moq` di setiap tahap |
| ✅ Tidak over-order | Max days coverage per status (fast=30, slow=14) |
| ✅ Dead stock = 0 | Hard-coded `qty = 0` untuk status dead |
| ✅ Tetap jalan tanpa AI | `generateFallbackDescription()` otomatis |
| ✅ Grand Total akurat | `editedNominalDifferences` track semua halaman |
