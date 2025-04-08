<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Tenant Management</h1>
      <button
        @click="showAddTenantModal = true"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
      >
        Add New Tenant
      </button>
    </div>

    <!-- Tenant Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="flex gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search tenants..."
          class="border rounded-lg px-3 py-2 flex-1"
        >
      </div>
    </div>

    <!-- Tenants Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="tenant in filteredTenants" :key="tenant.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ tenant.name }}</div>
              <div class="text-sm text-gray-500">{{ tenant.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ tenant.phone }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">Room {{ tenant.roomNumber }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ tenant.startDate }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ tenant.endDate }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editTenant(tenant)"
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                Edit
              </button>
              <button
                @click="deleteTenant(tenant.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Tenant Modal -->
    <div v-if="showAddTenantModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ editingTenant ? 'Edit Tenant' : 'Add New Tenant' }}</h2>
        <form @submit.prevent="saveTenant" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Full Name</label>
            <input
              v-model="tenantForm.name"
              type="text"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input
              v-model="tenantForm.email"
              type="email"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input
              v-model="tenantForm.phone"
              type="tel"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Room Number</label>
            <select
              v-model="tenantForm.roomNumber"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option v-for="room in availableRooms" :key="room.id" :value="room.number">
                Room {{ room.number }} - {{ room.type }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input
              v-model="tenantForm.startDate"
              type="date"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Documents</label>
            <input
              type="file"
              multiple
              class="mt-1 block w-full"
              @change="handleFileUpload"
            >
          </div>
          <div class="flex justify-end gap-4 mt-6">
            <button
              type="button"
              @click="showAddTenantModal = false"
              class="px-4 py-2 border rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              {{ editingTenant ? 'Save Changes' : 'Add Tenant' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TenantsView',
  data() {
    return {
      tenants: [
        {
          id: 1,
          name: 'John Doe',
          email: 'john@example.com',
          phone: '123-456-7890',
          roomNumber: '101',
          startDate: '2024-01-01',
          endDate: '2024-07-01',
          documents: []
        },
        // Add more tenants as needed
      ],
      rooms: [
        { id: 1, number: '101', type: 'Standard', status: 'Occupied' },
        { id: 2, number: '102', type: 'Deluxe', status: 'Available' },
        { id: 3, number: '201', type: 'Suite', status: 'Available' },
        // Add more rooms as needed
      ],
      showAddTenantModal: false,
      editingTenant: null,
      searchQuery: '',
      tenantForm: {
        name: '',
        email: '',
        phone: '',
        roomNumber: '',
        startDate: '',
        documents: []
      }
    }
  },
  computed: {
    filteredTenants() {
      return this.tenants.filter(tenant => {
        return !this.searchQuery || 
          tenant.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          tenant.email.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          tenant.roomNumber.includes(this.searchQuery)
      })
    },
    availableRooms() {
      return this.rooms.filter(room => room.status === 'Available')
    }
  },
  methods: {
    calculateEndDate(startDate) {
      const date = new Date(startDate)
      date.setMonth(date.getMonth() + 6)
      return date.toISOString().split('T')[0]
    },
    handleFileUpload(event) {
      this.tenantForm.documents = Array.from(event.target.files)
    },
    editTenant(tenant) {
      this.editingTenant = tenant
      this.tenantForm = { ...tenant }
      this.showAddTenantModal = true
    },
    deleteTenant(tenantId) {
      if (confirm('Are you sure you want to delete this tenant?')) {
        this.tenants = this.tenants.filter(tenant => tenant.id !== tenantId)
      }
    },
    saveTenant() {
      const tenantData = {
        ...this.tenantForm,
        endDate: this.calculateEndDate(this.tenantForm.startDate)
      }

      if (this.editingTenant) {
        const index = this.tenants.findIndex(tenant => tenant.id === this.editingTenant.id)
        this.tenants[index] = { ...this.editingTenant, ...tenantData }
      } else {
        const newTenant = {
          id: this.tenants.length + 1,
          ...tenantData
        }
        this.tenants.push(newTenant)
      }

      // Update room status
      const roomIndex = this.rooms.findIndex(room => room.number === tenantData.roomNumber)
      if (roomIndex !== -1) {
        this.rooms[roomIndex].status = 'Occupied'
      }

      this.showAddTenantModal = false
      this.editingTenant = null
      this.tenantForm = {
        name: '',
        email: '',
        phone: '',
        roomNumber: '',
        startDate: '',
        documents: []
      }
    }
  }
}
</script> 