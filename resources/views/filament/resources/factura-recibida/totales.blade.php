<div class="flex">
    <div class="p-4 bg-black rounded-lg shadow">
        <h3 class="text-lg font-semibold">Subtotal</h3>
        <p id="subtotal" class="text-2xl text-gray-800">
            ${{ number_format($subtotal, 2) }}
        </p>
    </div>
    <div class="p-4 bg-black rounded-lg shadow">
        <h3 class="text-lg font-semibold">IVA</h3>
        <p id="iva" class="text-2xl text-gray-800">
            ${{ number_format($iva, 2) }}
        </p>
    </div>
    <div class="p-4 bg-black rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total</h3>
        <p id="total" class="text-2xl text-gray-800">
            ${{ number_format($total, 2) }}
        </p>
    </div>
</div>
