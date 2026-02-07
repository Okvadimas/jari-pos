<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>58mm Thermal Receipt Preview</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2b8cee",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .thermal-paper {
            width: 320px;
            /* Approximately 58mm equivalent in screen pixels for preview */
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .dashed-line {
            border: none;
            border-top: 1px dashed #000;
            height: 1px;
            width: 100%;
        }

        /* Hide scrollbar for cleaner mobile look */
        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    <style>
        body {
            min-height: max(884px, 100dvh);
        }
        @media print {
            /* @page {
                margin: 0;
                padding: 24px;
                size: auto;
            } */

            /* Force background colors to print buat logo */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                min-height: auto;
            }
            .flex-col {
                display: block; /* Reset flex column which can mess up print flow */
            }
            .min-h-screen {
                min-height: auto;
            }
            .thermal-paper {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }
            
            /* Ensure the content itself has tiny padding if needed, but the wrapper shouldn't */
            .thermal-paper > * {
                /* Optional: check inner spacing */
            }
        }
    </style>
</head>

<body class="bg-white text-black font-display antialiased">
    <div class="flex flex-col items-center justify-center min-h-screen">
        
        <!-- The 58mm Thermal Receipt Container -->
        <!-- Changed width from 58mm to 100% max-w-[320px] for better screen/print rendering -->
        <div class="thermal-paper bg-white w-full max-w-[320px] p-4 text-black flex flex-col items-center">
            
            <!-- Logo & Store Info -->
            <div class="flex flex-col items-center mb-4">
                <div class="w-14 h-14 bg-black rounded-full flex items-center justify-center mb-2 shadow-sm">
                    <span class="material-symbols-outlined text-white text-3xl">restaurant</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight uppercase text-center leading-none mb-1">{{ $order->company_name ?? 'Modern Bistro' }}</h1>
                <p class="text-[11px] text-center mt-1 leading-tight text-slate-700 font-medium">{{ $order->company_address ?? '123 Tech Lane, Silicon Valley, CA' }}</p>
                <p class="text-[11px] text-center text-slate-700">Tel: (555) 012-3456</p>
            </div>
            
            <div class="dashed-line my-3 opacity-50"></div>
            
            <!-- Transaction Meta -->
            <div class="w-full text-[11px] space-y-1.5 font-medium text-slate-800">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 uppercase tracking-wider text-[10px]">Date</span>
                    <span>{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 uppercase tracking-wider text-[10px]">Receipt ID</span>
                    <span>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 uppercase tracking-wider text-[10px]">Cashier</span>
                    <span>{{ $order->created_by_name ?? 'Admin' }}</span>
                </div>
            </div>
            
            <div class="dashed-line my-3 opacity-50"></div>
            
            <!-- Line Items -->
            <div class="w-full space-y-3">
                @foreach($details as $item)
                <div class="flex flex-col">
                    <div class="flex justify-between items-start mb-0.5">
                        <span class="text-[12px] font-bold leading-tight w-[65%]">{{ $item->product_name }}</span>
                        <span class="text-[12px] font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if(isset($item->variant_name) && $item->variant_name)
                    <span class="text-[10px] text-slate-500 block -mt-0.5">{{ $item->variant_name }}</span>
                    @endif
                    <span class="text-[10px] text-slate-500 font-medium">{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            
            <div class="dashed-line my-3 opacity-50"></div>
            
            <!-- Totals Section -->
            <div class="w-full space-y-1.5">
                <div class="flex justify-between text-[11px] font-medium text-slate-700">
                    <span>Subtotal</span>
                    <span>{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                @if($order->total_discount_manual > 0)
                <div class="flex justify-between text-[11px] text-red-600 font-medium">
                    <span>Discount {{ $order->promo_name ? '('.$order->promo_name.')' : '' }}</span>
                    <span>-{{ number_format($order->total_discount_manual, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between text-lg font-black pt-2 mt-2 border-t-2 border-black border-dashed items-end">
                    <span class="text-xs uppercase tracking-wider font-bold">Total</span>
                    <span>{{ number_format($order->final_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="dashed-line my-3 opacity-50"></div>
            
            <!-- Payment Method -->
            @if(isset($order->payment_method_name))
            <div class="w-full text-[10px] font-medium text-slate-500 italic mb-4">
                <div class="flex justify-between">
                    <span>Payment via {{ $order->payment_method_name }}</span>
                </div>
            </div>
            @endif
            
            <!-- Unified JariPOS Promo Footer -->
            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col items-center w-full">
                <!-- Promo Box -->
                <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-3 w-full flex items-center justify-between gap-3 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-12 h-12 bg-blue-50/50 rounded-full blur-lg -mr-6 -mt-6"></div>
                    
                    <div class="flex flex-col items-start z-10">
                        <span class="text-[8px] uppercase tracking-wider text-slate-400 font-bold mb-1">Powered by</span>
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[#6576ff] text-[14px]">verified</span>
                            <span class="font-black text-sm tracking-tight text-slate-900 leading-none">JariPOS</span>
                        </div>
                        <span class="text-[8px] text-slate-500 font-medium mt-1">Scan for info &rarr;</span>
                    </div>

                    <!-- QR -->
                    <div class="bg-white p-1 rounded-md shadow-sm border border-slate-100 z-10">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://jaripos.com" 
                             alt="Scan" 
                             class="w-10 h-10 object-contain">
                    </div>
                </div>
            </div>

            <!-- Bottom Zig-Zag effect simulation -->
            <div class="absolute -bottom-2 left-0 w-full h-2 flex overflow-hidden">
                <!-- Zig zags removed for cleaner print, usually thermal cutter handles this -->
            </div>
        </div>
    </div>
    
    <!-- Auto-print script removed to prevent double dialogs. Printing is handled by POS JS. -->
</body>

</html>