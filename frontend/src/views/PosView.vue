<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Selection -->
    <div class="lg:col-span-2">
      <div class="card">
        <!-- Search Bar -->
        <div class="mb-4">
          <div class="flex gap-2">
            <input
              v-model="barcodeInput"
              @keyup.enter="searchByBarcode"
              type="text"
              class="input flex-1"
              placeholder="Scan barcode atau ketik SKU..."
            />
            <button @click="searchByBarcode" class="btn btn-primary">
              🔍 Cari
            </button>
          </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 max-h-[600px] overflow-y-auto">
          <div
            v-for="product in products"
            :key="product.id"
            @click="addProductToCart(product)"
            class="border rounded-lg p-3 hover:shadow-lg cursor-pointer transition-shadow"
            :class="product.stock <= 0 ? 'opacity-50 cursor-not-allowed' : ''"
          >
            <div class="aspect-square bg-gray-200 rounded mb-2 flex items-center justify-center text-4xl">
              📦
            </div>
            <h4 class="font-semibold text-sm mb-1 truncate">{{ product.name }}</h4>
            <p class="text-xs text-gray-600 mb-1">{{ product.category?.name }}</p>
            <p class="text-primary-600 font-bold">Rp {{ formatNumber(product.selling_price) }}</p>
            <p class="text-xs" :class="product.stock <= product.minimum_stock ? 'text-red-600' : 'text-gray-600'">
              Stok: {{ product.stock }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Cart & Checkout -->
    <div class="lg:col-span-1">
      <div class="card sticky top-6">
        <h3 class="text-lg font-bold mb-4">Keranjang Belanja</h3>

        <!-- Cart Items -->
        <div class="mb-4 max-h-64 overflow-y-auto">
          <div v-if="cartItems.length === 0" class="text-center text-gray-500 py-8">
            Keranjang masih kosong
          </div>

          <div v-for="item in cartItems" :key="item.product_id" class="border-b pb-3 mb-3">
            <div class="flex justify-between items-start mb-2">
              <h4 class="font-medium text-sm flex-1">{{ item.product_name }}</h4>
              <button @click="removeItem(item.product_id)" class="text-red-600 text-xs">
                ✕
              </button>
            </div>
            <div class="flex items-center gap-2 mb-2">
              <button
                @click="decreaseQuantity(item.product_id)"
                class="w-7 h-7 bg-gray-200 rounded hover:bg-gray-300"
              >
                -
              </button>
              <input
                :value="item.quantity"
                @input="updateQuantity(item.product_id, $event.target.value)"
                type="number"
                class="w-16 text-center border rounded py-1"
                min="1"
                :max="item.max_stock"
              />
              <button
                @click="increaseQuantity(item.product_id)"
                class="w-7 h-7 bg-gray-200 rounded hover:bg-gray-300"
              >
                +
              </button>
              <span class="text-sm">× Rp {{ formatNumber(item.price) }}</span>
            </div>
            <div class="text-sm text-gray-600">
              Subtotal: Rp {{ formatNumber(item.price * item.quantity) }}
            </div>
          </div>
        </div>

        <!-- Totals -->
        <div class="border-t pt-4 mb-4">
          <div class="flex justify-between mb-2">
            <span>Subtotal:</span>
            <span class="font-semibold">Rp {{ formatNumber(subtotal) }}</span>
          </div>
          <div class="flex justify-between mb-2">
            <span>Diskon:</span>
            <input
              v-model.number="discount"
              type="number"
              class="w-32 text-right border rounded px-2 py-1 text-sm"
              min="0"
              :max="subtotal"
            />
          </div>
          <div class="flex justify-between mb-2">
            <span>Pajak:</span>
            <input
              v-model.number="tax"
              type="number"
              class="w-32 text-right border rounded px-2 py-1 text-sm"
              min="0"
            />
          </div>
          <div class="flex justify-between text-lg font-bold border-t pt-2">
            <span>Total:</span>
            <span class="text-primary-600">Rp {{ formatNumber(total) }}</span>
          </div>
        </div>

        <!-- Payment -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Metode Pembayaran</label>
          <select v-model="paymentMethod" class="input">
            <option value="cash">Cash</option>
            <option value="transfer">Transfer</option>
            <option value="debit">Debit</option>
            <option value="credit">Credit</option>
          </select>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Jumlah Bayar</label>
          <input
            v-model.number="paidAmount"
            type="number"
            class="input"
            placeholder="0"
            :min="total"
          />
        </div>

        <div v-if="paidAmount >= total && paidAmount > 0" class="mb-4 p-3 bg-green-100 rounded">
          <div class="flex justify-between font-semibold text-green-800">
            <span>Kembalian:</span>
            <span>Rp {{ formatNumber(paidAmount - total) }}</span>
          </div>
        </div>

        <!-- Customer Info (Optional) -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Nama Pelanggan (Opsional)</label>
          <input v-model="customerName" type="text" class="input" placeholder="Nama pelanggan" />
        </div>

        <!-- Checkout Button -->
        <button
          @click="checkout"
          :disabled="cartItems.length === 0 || paidAmount < total || loading"
          class="w-full btn btn-success py-3 text-lg"
        >
          {{ loading ? 'Memproses...' : '💳 Bayar' }}
        </button>

        <button
          @click="clearCartItems"
          class="w-full btn btn-danger mt-2"
          :disabled="cartItems.length === 0"
        >
          🗑️ Bersihkan Keranjang
        </button>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
      <div class="text-center">
        <div class="text-6xl mb-4">✅</div>
        <h3 class="text-2xl font-bold mb-2">Transaksi Berhasil!</h3>
        <p class="text-gray-600 mb-4">Invoice: {{ lastTransaction?.invoice_number }}</p>
        <p class="text-3xl font-bold text-primary-600 mb-6">
          Rp {{ formatNumber(lastTransaction?.total || 0) }}
        </p>
        <div class="flex gap-2">
          <button @click="printReceipt" class="flex-1 btn btn-primary">
            🖨️ Cetak Struk
          </button>
          <button @click="closeModal" class="flex-1 btn btn-secondary">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProductStore } from '@/stores/product'
import { useTransactionStore } from '@/stores/transaction'

const productStore = useProductStore()
const transactionStore = useTransactionStore()

const products = ref([])
const barcodeInput = ref('')
const discount = ref(0)
const tax = ref(0)
const paymentMethod = ref('cash')
const paidAmount = ref(0)
const customerName = ref('')
const loading = ref(false)
const showSuccessModal = ref(false)
const lastTransaction = ref(null)

const cartItems = computed(() => transactionStore.cartItems)
const subtotal = computed(() => transactionStore.cartTotal)
const total = computed(() => subtotal.value - discount.value + tax.value)

onMounted(async () => {
  await loadProducts()
})

async function loadProducts() {
  await productStore.fetchProducts({ per_page: 100, is_active: true })
  products.value = productStore.products
}

async function searchByBarcode() {
  if (!barcodeInput.value.trim()) return

  const result = await productStore.searchByBarcode(barcodeInput.value)
  if (result.success) {
    addProductToCart(result.data)
    barcodeInput.value = ''
  } else {
    alert('Produk tidak ditemukan')
  }
}

function addProductToCart(product) {
  if (product.stock <= 0) {
    alert('Stok habis')
    return
  }

  const result = transactionStore.addToCart(product)
  if (!result.success) {
    alert(result.message)
  }
}

function updateQuantity(productId, value) {
  const qty = parseInt(value)
  if (!isNaN(qty) && qty > 0) {
    const result = transactionStore.updateCartItem(productId, qty)
    if (!result.success) {
      alert(result.message)
    }
  }
}

function increaseQuantity(productId) {
  const item = cartItems.value.find(i => i.product_id === productId)
  if (item) {
    updateQuantity(productId, item.quantity + 1)
  }
}

function decreaseQuantity(productId) {
  const item = cartItems.value.find(i => i.product_id === productId)
  if (item && item.quantity > 1) {
    updateQuantity(productId, item.quantity - 1)
  }
}

function removeItem(productId) {
  transactionStore.removeFromCart(productId)
}

function clearCartItems() {
  if (confirm('Hapus semua item di keranjang?')) {
    transactionStore.clearCart()
    discount.value = 0
    tax.value = 0
    paidAmount.value = 0
    customerName.value = ''
  }
}

async function checkout() {
  if (cartItems.value.length === 0) {
    alert('Keranjang masih kosong')
    return
  }

  if (paidAmount.value < total.value) {
    alert('Jumlah bayar kurang')
    return
  }

  loading.value = true

  const transactionData = {
    items: cartItems.value,
    discount: discount.value,
    tax: tax.value,
    paid_amount: paidAmount.value,
    payment_method: paymentMethod.value,
    customer_name: customerName.value || null,
  }

  const result = await transactionStore.createTransaction(transactionData)

  if (result.success) {
    lastTransaction.value = result.data
    showSuccessModal.value = true
    
    // Reset form
    discount.value = 0
    tax.value = 0
    paidAmount.value = 0
    customerName.value = ''
    
    // Reload products to update stock
    await loadProducts()
  } else {
    alert('Transaksi gagal: ' + result.message)
  }

  loading.value = false
}

function printReceipt() {
  // Simple print implementation
  const printWindow = window.open('', '', 'width=300,height=600')
  printWindow.document.write(`
    <html>
      <head>
        <title>Struk Pembayaran</title>
        <style>
          body { font-family: monospace; font-size: 12px; padding: 10px; }
          .center { text-align: center; }
          .line { border-top: 1px dashed #000; margin: 10px 0; }
        </style>
      </head>
      <body>
        <div class="center">
          <h2>TOKO PERTANIAN SINAR JAYA</h2>
          <p>Jl. Pertanian No. 123</p>
          <p>Telp: 0812-3456-7890</p>
        </div>
        <div class="line"></div>
        <p>Invoice: ${lastTransaction.value?.invoice_number}</p>
        <p>Tanggal: ${new Date().toLocaleString('id-ID')}</p>
        <p>Kasir: ${lastTransaction.value?.user?.name}</p>
        ${customerName.value ? `<p>Pelanggan: ${customerName.value}</p>` : ''}
        <div class="line"></div>
        ${lastTransaction.value?.items.map(item => `
          <p>${item.product_name}<br/>
          ${item.quantity} x ${formatNumber(item.price)} = ${formatNumber(item.total)}</p>
        `).join('')}
        <div class="line"></div>
        <p>Subtotal: Rp ${formatNumber(lastTransaction.value?.subtotal)}</p>
        <p>Diskon: Rp ${formatNumber(lastTransaction.value?.discount)}</p>
        <p>Pajak: Rp ${formatNumber(lastTransaction.value?.tax)}</p>
        <p><strong>TOTAL: Rp ${formatNumber(lastTransaction.value?.total)}</strong></p>
        <p>Bayar: Rp ${formatNumber(lastTransaction.value?.paid_amount)}</p>
        <p>Kembali: Rp ${formatNumber(lastTransaction.value?.change_amount)}</p>
        <div class="line"></div>
        <p class="center">Terima Kasih</p>
        <p class="center">Selamat Berbelanja Kembali</p>
      </body>
    </html>
  `)
  printWindow.document.close()
  printWindow.print()
}

function closeModal() {
  showSuccessModal.value = false
  lastTransaction.value = null
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}
</script>
