flowchart TB
    subgraph Trigger["⏰ Trigger"]
        CRON["Cron Job\nSetiap 00:10"]
        MANUAL["Tombol Manual\nUI: Proses Laporan"]
    end

    subgraph Command["🔧 Artisan Command"]
        CMD["stock:calculate-moving\n--company, --period"]
    end

    subgraph Service["⚙️ MovingStockService"]
        CALC["calculate()"]
        NORM["normalize()\nMin-Max Scaling"]
        CLASSIFY["classify()\nFast / Medium / Slow / Dead"]
        BULK["updateVariantStatuses()\nBulk SQL UPDATE"]
    end

    subgraph Repository["📦 Repository Layer"]
        SALES["SalesRepository\ngetSalesPerVariant()"]
        PV["ProductVariantRepository\ngetAllActive() / getLatestStock()"]
        REC["RecommendationRepository\nsaveHistory() / saveDetails()"]
    end

    subgraph DB["🗄️ Database"]
        SO[("sales_orders\n+ details")]
        SDB[("stock_daily_balances")]
        PVT[("product_variants\nmoving_status, moving_score")]
        SMH[("stock_moving_histories")]
        SMD[("stock_moving_details")]
    end

    subgraph Frontend["🖥️ Frontend"]
        CARDS["Summary Cards\n🟢🟡🟠🔴"]
        TABLE["DataTable\n+ Filter Pills"]
        HISTORY["Riwayat Analisis"]
    end

    %% Flow Connections
    CRON --> CMD
    MANUAL -->|"POST /generate"| CALC
    CMD --> CALC
    
    CALC --> SALES
    CALC --> PV
    
    CALC --> NORM
    NORM --> CLASSIFY
    CLASSIFY --> BULK
    CALC --> REC

    SALES --> SO
    PV --> SDB
    PV --> PVT
    
    REC --> SMH
    REC --> SMD
    
    BULK --> PVT

    %% UI Connections
    REC -->|"GET /summary"| CARDS
    REC -->|"GET /datatable"| TABLE
    REC -->|"index"| HISTORY

    %% Styling for better visualization
    style Trigger fill:#fdf,stroke:#333
    style Service fill:#eef,stroke:#333
    style DB fill:#efe,stroke:#333
    style Frontend fill:#fff4dd,stroke:#d4a017