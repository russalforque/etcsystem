<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h1 class="text-2xl font-bold">Contract Management</h1>
      <button
        @click="showGenerateContractModal = true"
        class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
      >
        Generate New Contract
      </button>
    </div>

    <!-- Contract Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="flex flex-col sm:flex-row gap-4">
        <select
          v-model="statusFilter"
          class="w-full sm:w-auto border rounded-lg px-3 py-2"
        >
          <option value="">All Contracts</option>
          <option value="Active">Active</option>
          <option value="Expired">Expired</option>
          <option value="Expiring Soon">Expiring Soon</option>
        </select>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search contracts..."
          class="w-full sm:w-auto border rounded-lg px-3 py-2"
        >
      </div>
    </div>

    <!-- Contracts Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
      <div
        v-for="contract in filteredContracts"
        :key="contract.id"
        class="bg-white p-4 lg:p-6 rounded-lg shadow"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-lg lg:text-xl font-semibold">{{ contract.tenantName }}</h3>
            <p class="text-sm text-gray-500">Room {{ contract.roomNumber }}</p>
          </div>
          <span :class="getStatusClass(contract.status)" class="px-2 py-1 rounded-full text-xs">
            {{ contract.status }}
          </span>
        </div>
        <div class="space-y-2">
          <p><span class="font-medium">Start Date:</span> {{ contract.startDate }}</p>
          <p><span class="font-medium">End Date:</span> {{ contract.endDate }}</p>
          <p><span class="font-medium">Monthly Rent:</span> ${{ contract.monthlyRent }}</p>
          <p><span class="font-medium">Security Deposit:</span> ${{ contract.securityDeposit }}</p>
        </div>
        <div class="mt-4 flex gap-2">
          <button
            @click="viewContract(contract)"
            class="text-blue-600 hover:text-blue-800"
          >
            View
          </button>
          <button
            @click="editContract(contract)"
            class="text-blue-600 hover:text-blue-800"
          >
            Edit
          </button>
        </div>
      </div>
    </div>

    <!-- Generate Contract Modal -->
    <div v-if="showGenerateContractModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Generate New Contract</h2>
        <form @submit.prevent="generateContract" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Select Tenant</label>
            <select
              v-model="contractForm.tenantId"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option v-for="tenant in availableTenants" :key="tenant.id" :value="tenant.id">
                {{ tenant.name }} - Room {{ tenant.roomNumber }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input
              v-model="contractForm.startDate"
              type="date"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Monthly Rent</label>
            <input
              v-model="contractForm.monthlyRent"
              type="number"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Security Deposit</label>
            <input
              v-model="contractForm.securityDeposit"
              type="number"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div class="flex justify-end gap-4 mt-6">
            <button
              type="button"
              @click="showGenerateContractModal = false"
              class="px-4 py-2 border rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Generate Contract
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Contract Modal -->
    <div v-if="selectedContract" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-2xl">
        <div class="flex justify-between items-start mb-4">
          <h2 class="text-xl font-bold">Contract Details</h2>
          <button
            @click="selectedContract = null"
            class="text-gray-500 hover:text-gray-700"
          >
            ×
          </button>
        </div>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-medium text-gray-500">Tenant Information</h3>
              <p class="mt-1">{{ selectedContract.tenantName }}</p>
              <p class="text-sm text-gray-500">{{ selectedContract.tenantEmail }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Room Information</h3>
              <p class="mt-1">Room {{ selectedContract.roomNumber }}</p>
              <p class="text-sm text-gray-500">Monthly Rent: ${{ selectedContract.monthlyRent }}</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-medium text-gray-500">Contract Period</h3>
              <p class="mt-1">Start: {{ selectedContract.startDate }}</p>
              <p class="text-sm text-gray-500">End: {{ selectedContract.endDate }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Security Deposit</h3>
              <p class="mt-1">${{ selectedContract.securityDeposit }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContractsView',
  data() {
    return {
      contracts: [
        {
          id: 1,
          tenantId: 1,
          tenantName: 'John Doe',
          tenantEmail: 'john@example.com',
          roomNumber: '101',
          startDate: '2024-01-01',
          endDate: '2024-07-01',
          monthlyRent: 800,
          securityDeposit: 1600,
          status: 'Active'
        },
        {
          id: 2,
          tenantId: 2,
          tenantName: 'Jane Smith',
          tenantEmail: 'jane@example.com',
          roomNumber: '203',
          startDate: '2024-02-01',
          endDate: '2024-08-01',
          monthlyRent: 1000,
          securityDeposit: 2000,
          status: 'Active'
        },
        {
          id: 3,
          tenantId: 3,
          tenantName: 'Mike Johnson',
          tenantEmail: 'mike@example.com',
          roomNumber: '305',
          startDate: '2023-12-01',
          endDate: '2024-06-01',
          monthlyRent: 1200,
          securityDeposit: 2400,
          status: 'Expiring Soon'
        }
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
      showGenerateContractModal: false,
      selectedContract: null,
      statusFilter: '',
      searchQuery: '',
      contractForm: {
        tenantId: '',
        startDate: '',
        monthlyRent: '',
        securityDeposit: ''
      }
    }
  },
  computed: {
    filteredContracts() {
      return this.contracts.filter(contract => {
        const matchesStatus = !this.statusFilter || contract.status === this.statusFilter
        const matchesSearch = !this.searchQuery || 
          contract.tenantName.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          contract.roomNumber.includes(this.searchQuery)
        return matchesStatus && matchesSearch
      })
    },
    availableTenants() {
      return this.tenants.filter(tenant => {
        const hasActiveContract = this.contracts.some(
          contract => contract.tenantId === tenant.id && contract.status === 'Active'
        )
        return !hasActiveContract
      })
    }
  },
  methods: {
    getStatusClass(status) {
      const classes = {
        'Active': 'bg-green-100 text-green-800',
        'Expired': 'bg-red-100 text-red-800',
        'Expiring Soon': 'bg-yellow-100 text-yellow-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },
    calculateEndDate(startDate) {
      const date = new Date(startDate)
      date.setMonth(date.getMonth() + 6)
      return date.toISOString().split('T')[0]
    },
    generateContract() {
      const tenant = this.tenants.find(t => t.id === this.contractForm.tenantId)
      const newContract = {
        id: this.contracts.length + 1,
        tenantId: this.contractForm.tenantId,
        tenantName: tenant.name,
        tenantEmail: tenant.email,
        roomNumber: tenant.roomNumber,
        startDate: this.contractForm.startDate,
        endDate: this.calculateEndDate(this.contractForm.startDate),
        monthlyRent: this.contractForm.monthlyRent,
        securityDeposit: this.contractForm.securityDeposit,
        status: 'Active'
      }
      this.contracts.push(newContract)
      this.showGenerateContractModal = false
      this.contractForm = {
        tenantId: '',
        startDate: '',
        monthlyRent: '',
        securityDeposit: ''
      }
    },
    viewContract(contract) {
      this.selectedContract = contract
    },
    editContract(contract) {
      // Implement edit functionality
      console.log('Edit contract:', contract)
    }
  }
}
</script> 