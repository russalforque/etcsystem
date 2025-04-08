<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h1 class="text-2xl font-bold">Payment Management</h1>
      <button
        @click="showAddPaymentModal = true"
        class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
      >
        Record Payment
      </button>
    </div>

    <!-- Payment Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
      <div class="bg-white p-4 lg:p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Collected</h3>
        <p class="text-xl lg:text-2xl font-bold">${{ totalCollected }}</p>
      </div>
      <div class="bg-white p-4 lg:p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Pending Payments</h3>
        <p class="text-xl lg:text-2xl font-bold">${{ totalPending }}</p>
      </div>
      <div class="bg-white p-4 lg:p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Late Fees</h3>
        <p class="text-xl lg:text-2xl font-bold">${{ totalLateFees }}</p>
      </div>
      <div class="bg-white p-4 lg:p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Overdue Payments</h3>
        <p class="text-xl lg:text-2xl font-bold">{{ overdueCount }}</p>
      </div>
    </div>

    <!-- Payment Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="flex flex-col sm:flex-row gap-4">
        <select
          v-model="statusFilter"
          class="w-full sm:w-auto border rounded-lg px-3 py-2"
        >
          <option value="">All Payments</option>
          <option value="Paid">Paid</option>
          <option value="Pending">Pending</option>
          <option value="Overdue">Overdue</option>
        </select>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search payments..."
          class="w-full sm:w-auto border rounded-lg px-3 py-2"
        >
      </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="payment in filteredPayments" :key="payment.id">
            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ payment.tenantName }}</div>
            </td>
            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-500">Room {{ payment.roomNumber }}</div>
            </td>
            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">${{ payment.amount }}</div>
            </td>
            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-500">{{ payment.dueDate }}</div>
            </td>
            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(payment.status)" class="px-2 py-1 text-xs rounded-full">
                {{ payment.status }}
              </span>
            </td>
            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm">
              <button @click="viewPayment(payment)" class="text-blue-600 hover:text-blue-800 mr-3">View</button>
              <button @click="editPayment(payment)" class="text-blue-600 hover:text-blue-800">Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Record Payment Modal -->
    <div v-if="showAddPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Record Payment</h2>
        <form @submit.prevent="savePayment" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Select Tenant</label>
            <select
              v-model="paymentForm.tenantId"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                {{ tenant.name }} - Room {{ tenant.roomNumber }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Payment Date</label>
            <input
              v-model="paymentForm.paymentDate"
              type="date"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Amount</label>
            <input
              v-model="paymentForm.amount"
              type="number"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select
              v-model="paymentForm.paymentMethod"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option value="Cash">Cash</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="Credit Card">Credit Card</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Reference Number</label>
            <input
              v-model="paymentForm.referenceNumber"
              type="text"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              placeholder="Optional"
            >
          </div>
          <div class="flex justify-end gap-4 mt-6">
            <button
              type="button"
              @click="showAddPaymentModal = false"
              class="px-4 py-2 border rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Save Payment
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Receipt Modal -->
    <div v-if="selectedPayment" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <div class="flex justify-between items-start mb-4">
          <h2 class="text-xl font-bold">Payment Receipt</h2>
          <button
            @click="selectedPayment = null"
            class="text-gray-500 hover:text-gray-700"
          >
            ×
          </button>
        </div>
        <div class="space-y-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500">Tenant Information</h3>
            <p class="mt-1">{{ selectedPayment.tenantName }}</p>
            <p class="text-sm text-gray-500">Room {{ selectedPayment.roomNumber }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Payment Details</h3>
            <p class="mt-1">Amount: ${{ selectedPayment.amount }}</p>
            <p class="text-sm text-gray-500">Date: {{ selectedPayment.paymentDate }}</p>
            <p class="text-sm text-gray-500">Method: {{ selectedPayment.paymentMethod }}</p>
            <p v-if="selectedPayment.referenceNumber" class="text-sm text-gray-500">
              Reference: {{ selectedPayment.referenceNumber }}
            </p>
          </div>
          <div class="flex justify-end mt-6">
            <button
              @click="downloadReceipt(selectedPayment)"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
            >
              Download Receipt
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PaymentsView',
  data() {
    return {
      payments: [
        {
          id: 1,
          tenantId: 1,
          tenantName: 'John Doe',
          tenantEmail: 'john@example.com',
          roomNumber: '101',
          dueDate: '2024-04-01',
          amount: 800,
          lateFee: 0,
          status: 'Paid',
          paymentDate: '2024-03-28',
          paymentMethod: 'Bank Transfer',
          referenceNumber: 'TRX123456'
        },
        // Add more payments as needed
      ],
      tenants: [
        {
          id: 1,
          name: 'John Doe',
          roomNumber: '101',
          email: 'john@example.com'
        },
        // Add more tenants as needed
      ],
      showAddPaymentModal: false,
      selectedPayment: null,
      statusFilter: '',
      searchQuery: '',
      paymentForm: {
        tenantId: '',
        paymentDate: '',
        amount: '',
        paymentMethod: 'Bank Transfer',
        referenceNumber: ''
      }
    }
  },
  computed: {
    filteredPayments() {
      return this.payments.filter(payment => {
        const matchesStatus = !this.statusFilter || payment.status === this.statusFilter
        const matchesSearch = !this.searchQuery || 
          payment.tenantName.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          payment.roomNumber.includes(this.searchQuery)
        return matchesStatus && matchesSearch
      })
    },
    totalCollected() {
      return this.payments
        .filter(payment => payment.status === 'Paid')
        .reduce((sum, payment) => sum + payment.amount, 0)
    },
    totalPending() {
      return this.payments
        .filter(payment => payment.status === 'Pending')
        .reduce((sum, payment) => sum + payment.amount, 0)
    },
    totalLateFees() {
      return this.payments
        .reduce((sum, payment) => sum + payment.lateFee, 0)
    },
    overdueCount() {
      return this.payments.filter(payment => payment.status === 'Overdue').length
    }
  },
  methods: {
    getStatusClass(status) {
      const classes = {
        'Paid': 'bg-green-100 text-green-800',
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Overdue': 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },
    calculateLateFee(payment) {
      if (payment.status === 'Overdue') {
        const dueDate = new Date(payment.dueDate)
        const today = new Date()
        const daysLate = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24))
        return Math.max(0, daysLate * 10) // $10 per day late fee
      }
      return 0
    },
    recordPayment(payment) {
      this.paymentForm = {
        tenantId: payment.tenantId,
        paymentDate: new Date().toISOString().split('T')[0],
        amount: payment.amount,
        paymentMethod: 'Bank Transfer',
        referenceNumber: ''
      }
      this.showAddPaymentModal = true
    },
    savePayment() {
      const tenant = this.tenants.find(t => t.id === this.paymentForm.tenantId)
      const newPayment = {
        id: this.payments.length + 1,
        tenantId: this.paymentForm.tenantId,
        tenantName: tenant.name,
        tenantEmail: tenant.email,
        roomNumber: tenant.roomNumber,
        dueDate: this.paymentForm.paymentDate,
        amount: this.paymentForm.amount,
        lateFee: 0,
        status: 'Paid',
        paymentDate: this.paymentForm.paymentDate,
        paymentMethod: this.paymentForm.paymentMethod,
        referenceNumber: this.paymentForm.referenceNumber
      }
      this.payments.push(newPayment)
      this.showAddPaymentModal = false
      this.paymentForm = {
        tenantId: '',
        paymentDate: '',
        amount: '',
        paymentMethod: 'Bank Transfer',
        referenceNumber: ''
      }
    },
    viewPayment(payment) {
      this.selectedPayment = payment
    },
    editPayment(payment) {
      // Implement edit functionality
      console.log('Edit payment:', payment)
    },
    downloadReceipt(payment) {
      // Implement receipt download functionality
      console.log('Downloading receipt:', payment)
    }
  }
}
</script> 