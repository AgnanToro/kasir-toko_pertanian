<template>
  <div>
    <div class="card max-w-4xl mx-auto">
      <div v-if="loading" class="text-center py-8">Loading...</div>

      <div v-else-if="transaction">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
          <div>
            <h2 class="text-2xl font-bold mb-2">Detail Transaksi</h2>
            <p class="text-gray-600">{{ transaction.invoice_number }}</p>
          </div>
          <div class="text-right">
            <div class="text-sm text-gray-600">{{ formatDate(transaction.transaction_date) }}</div>
            <span class="badge badge-info mt-2">
              {{ transaction.payment_method.toUpperCase() }}
            </span>
          </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded">
          <div>
            <p class="text-sm text-gray-600">Kasir</p>
            <p class="font-semibold">{{ transaction.user?.name }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Pelanggan</p>
            <p class="font-semibold">{{ transaction.customer_name || '-' }}</p>
          </div>
        </div>

        <!-- Items -->
        <div class="mb-6">
          <h3 class="font-bold text-lg mb-3">Item Pembelian</h3>
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>SKU</th>
                  <th>Harga</th>
                  <th>Qty</th>
                  <th>Diskon</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in transaction.items" :key="item.id">
                  <td>{{ item.product_name }}</td>
                  <td>{{ item.product_sku }}</td>
                  <td>Rp {{ formatNumber(item.price) }}</td>
                  <td>{{ item.quantity }}</td>
                  <td>Rp {{ formatNumber(item.discount) }}</td>
                  <td class="font-semibold">Rp {{ formatNumber(item.total) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Totals -->
        <div class="border-t pt-4">
          <div class="max-w-md ml-auto space-y-2">
            <div class="flex justify-between">
              <span>Subtotal:</span>
              <span class="font-semibold">Rp {{ formatNumber(transaction.subtotal) }}</span>
            </div>
            <div class="flex justify-between text-red-600">
              <span>Diskon:</span>
              <span class="font-semibold">- Rp {{ formatNumber(transaction.discount) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Pajak:</span>
              <span class="font-semibold">Rp {{ formatNumber(transaction.tax) }}</span>
            </div>
            <div class="flex justify-between text-xl font-bold border-t pt-2">
              <span>Total:</span>
              <span class="text-primary-600">Rp {{ formatNumber(transaction.total) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Dibayar:</span>
              <span>Rp {{ formatNumber(transaction.paid_amount) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Kembalian:</span>
              <span>Rp {{ formatNumber(transaction.change_amount) }}</span>
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div v-if="transaction.notes" class="mt-6 p-4 bg-yellow-50 rounded">
          <p class="text-sm font-semibold mb-1">Catatan:</p>
          <p class="text-sm">{{ transaction.notes }}</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-6">
          <button @click="printReceipt" class="btn btn-primary">
            🖨️ Cetak Struk
          </button>
          <RouterLink to="/transactions" class="btn btn-secondary">
            ← Kembali
          </RouterLink>
        </div>
      </div>

      <div v-else class="text-center py-8 text-gray-500">
        Transaksi tidak ditemukan
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useTransactionStore } from '@/stores/transaction'

const route = useRoute()
const transactionStore = useTransactionStore()

const transaction = computed(() => transactionStore.transaction)
const loading = computed(() => transactionStore.loading)

onMounted(async () => {
  await transactionStore.fetchTransaction(route.params.id)
})

function printReceipt() {
  if (!transaction.value) return

  const printWindow = window.open('', '', 'width=300,height=600')
  printWindow.document.write(`
    <html>
      <head>
        <title>Struk - ${transaction.value.invoice_number}</title>
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
        <p>Invoice: ${transaction.value.invoice_number}</p>
        <p>Tanggal: ${formatDate(transaction.value.transaction_date)}</p>
        <p>Kasir: ${transaction.value.user?.name}</p>
        ${transaction.value.customer_name ? `<p>Pelanggan: ${transaction.value.customer_name}</p>` : ''}
        <div class="line"></div>
        ${transaction.value.items.map(item => `
          <p>${item.product_name}<br/>
          ${item.quantity} x ${formatNumber(item.price)} = ${formatNumber(item.total)}</p>
        `).join('')}
        <div class="line"></div>
        <p>Subtotal: Rp ${formatNumber(transaction.value.subtotal)}</p>
        <p>Diskon: Rp ${formatNumber(transaction.value.discount)}</p>
        <p>Pajak: Rp ${formatNumber(transaction.value.tax)}</p>
        <p><strong>TOTAL: Rp ${formatNumber(transaction.value.total)}</strong></p>
        <p>Bayar: Rp ${formatNumber(transaction.value.paid_amount)}</p>
        <p>Kembali: Rp ${formatNumber(transaction.value.change_amount)}</p>
        <div class="line"></div>
        <p class="center">Terima Kasih</p>
        <p class="center">Selamat Berbelanja Kembali</p>
      </body>
    </html>
  `)
  printWindow.document.close()
  printWindow.print()
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
