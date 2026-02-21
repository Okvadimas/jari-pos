<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>80mm Thermal Receipt - Light</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
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
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .thermal-paper {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
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

            .no-print {
                display: none;
            }

            body {
                background: white;
                min-height: auto;
            }

            .flex-col {
                display: block;
            }
            .min-h-screen {
                min-height: auto;
            }
            
            .thermal-paper {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
            }
        }
    </style>
    <style>
        body {
            min-height: max(884px, 100dvh);
        }
    </style>
</head>

<body class="bg-white text-black font-display antialiased">

    <!-- Main Receipt Container -->
    <div class="flex flex-col items-center justify-center min-h-screen">

        <!-- The Virtual 80mm/58mm Thermal Paper -->
        <div class="thermal-paper bg-white {{ $paperSize == '58' ? 'w-[58mm] text-[9px]' : 'w-[80mm]' }} p-4 text-black flex flex-col items-center">

            <!-- Logo & Header -->
            <div class="flex flex-col items-center mb-6">
                <div class="w-16 h-16 bg-black rounded-xl mb-4 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-4xl">local_mall</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight text-center uppercase">
                    {{ $order->company_name ?? 'Urban Goods Co.' }}</h1>
                <p class="text-[10px] text-center mt-1 leading-relaxed opacity-80 uppercase tracking-widest">
                    {{ $order->company_address ?? '123 Design District, NY 10012' }}<br />
                    www.urbangoods.co
                </p>
                <div class="flex gap-3 mt-2">
                    <span class="text-[9px] font-medium opacity-60">@urbangoodsco</span>
                </div>
            </div>

            <!-- Transaction Metadata -->
            <div class="w-full grid grid-cols-2 gap-y-1 py-4 border-t border-b border-dashed border-slate-300 mb-6">
                <div class="text-[10px] uppercase tracking-tighter opacity-60">Receipt ID:</div>
                <div class="text-[10px] font-semibold text-right">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="text-[10px] uppercase tracking-tighter opacity-60">Date:</div>
                <div class="text-[10px] font-semibold text-right">
                    {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                </div>
                <div class="text-[10px] uppercase tracking-tighter opacity-60">Time:</div>
                <div class="text-[10px] font-semibold text-right">
                    {{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}
                </div>
                <div class="text-[10px] uppercase tracking-tighter opacity-60">Cashier:</div>
                <div class="text-[10px] font-semibold text-right">{{ $order->created_by_name ?? 'Admin' }}</div>
            </div>

            <!-- Itemized List -->
            <div class="w-full mb-6">
                <div class="grid grid-cols-12 gap-1 {{ $paperSize == '58' ? 'text-[8px]' : 'text-[9px]' }} font-bold uppercase border-b border-black pb-1 mb-2">
                    <div class="col-span-6">Item</div>
                    <div class="col-span-2 text-center">Qty</div>
                    <div class="col-span-2 text-right">Price</div>
                    <div class="col-span-2 text-right">Total</div>
                </div>
                <!-- Items -->
                <div class="space-y-3">
                    @foreach($details as $item)
                        <div class="grid grid-cols-12 gap-1 items-start {{ $paperSize == '58' ? 'text-[9px]' : 'text-[10px]' }}">
                            <div class="col-span-6 font-medium leading-tight">
                                {{ $item->product_name }}
                                @if(isset($item->variant_name) && $item->variant_name)
                                    <span class="text-[8px] text-gray-500 block">{{ $item->variant_name }}</span>
                                @endif
                            </div>
                            <div class="col-span-2 text-center">{{ $item->quantity }}</div>
                            <div class="col-span-2 text-right">{{ number_format($item->sell_price, 0, ',', '.') }}</div>
                            <div class="col-span-2 text-right font-semibold">
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Totals Section -->
            <div class="w-full flex flex-col gap-1 border-t border-slate-200 pt-4 mb-8">
                <div class="flex justify-between {{ $paperSize == '58' ? 'text-[9px]' : 'text-[10px]' }}">
                    <span class="opacity-60 uppercase">Subtotal</span>
                    <span>{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-[10px] text-red-600 font-medium">
                        <span class="uppercase">Discount
                            {{ $order->promo_name ? '(' . $order->promo_name . ')' : '' }}</span>
                        <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between items-end mt-2 pt-2 border-t-2 border-black">
                    <span class="text-sm font-bold uppercase tracking-tight">Total Amount</span>
                    <span class="text-xl font-black">{{ number_format($order->final_amount, 0, ',', '.') }}</span>
                </div>

                @if(isset($order->payment_method_name))
                    <div class="flex justify-between text-[9px] mt-1 text-gray-500">
                        <span class="uppercase">Paid via</span>
                        <span class="uppercase font-bold">{{ $order->payment_method_name }}</span>
                    </div>
                @endif
            </div>

            <!-- Return Policy & QR Section -->
            <div class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 mb-6">
                <h4 class="text-[9px] font-bold uppercase mb-1">Return Policy</h4>
                <p class="text-[8px] leading-relaxed opacity-70">
                    Returns accepted within 14 days with original receipt. Items must be in original packaging and
                    unworn condition.
                </p>
            </div>
            <!-- Footer Message -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-[1px] bg-slate-200 mb-3"></div>
                <p class="text-base font-medium italic text-center leading-tight font-serif text-slate-800">See you again!</p>
                <p class="text-[9px] text-slate-400 mt-1 uppercase tracking-[0.2em] mb-6">Thank you for shopping local</p>

                <!-- Unified JariPOS Promo Section -->
                <div class="w-full bg-slate-50/50 rounded-xl border border-dashed border-slate-200 p-4 flex flex-col items-center relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-blue-50 rounded-full blur-xl -mr-8 -mt-8 opacity-50"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-purple-50 rounded-full blur-xl -ml-8 -mb-8 opacity-50"></div>
                    
                    <p class="text-[8px] uppercase tracking-widest text-slate-400 font-bold mb-3 z-10">Powered by System</p>
                    
                    <div class="flex items-center gap-4 z-10">
                        <!-- QR -->
                        <div class="bg-white p-1 roundedshadow-sm border border-slate-100">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://jaripos.com" 
                                 alt="JariPOS" 
                                 class="w-12 h-12 object-contain mix-blend-multiply">
                        </div>
                        
                        <!-- Brand Text -->
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center gap-1 mb-0.5">
                                <span class="material-symbols-outlined text-[#6576ff] text-[18px]">verified</span>
                                <span class="font-black text-lg tracking-tight text-slate-800 leading-none">JariPOS</span>
                            </div>
                            <span class="text-[8px] text-slate-500 font-medium leading-none">The Best POS for Your Business</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Auto-print script removed to prevent double dialogs. Printing is handled by POS JS. -->
</body>

</html>