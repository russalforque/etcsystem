<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h1 class="text-2xl font-bold">Maintenance Requests</h1>
      <button
        @click="showAddRequestModal = true"
        class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
      >
        Submit New Request
      </button>
    </div>

    <!-- Maintenance Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
      <div class="bg-white p-4 lg:p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Requests</h3>
        <p class="text-xl lg:text-2xl font-bold">{{ totalRequests }}</p>
      </div>
      <div class="bg-white p-4 lg:p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Pending Requests</h3>
        <p class="text-xl lg:text-2xl font-bold">{{ pendingRequests }}</p>
      </div>
      <div class="bg-white p-4 lg:p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">In Progress</h3>
        <p class="text-xl lg:text-2xl font-bold">{{ inProgressRequests }}</p>
      </div>
      <div class="bg-white p-4 lg:p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Completed</h3>
        <p class="text-xl lg:text-2xl font-bold">{{ completedRequests }}</p>
      </div>
    </div>

    <!-- Request Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="flex flex-col sm:flex-row gap-4">
        <select
          v-model="statusFilter"
          class="w-full sm:w-auto border rounded-lg px-3 py-2"
        >
          <option value="">All Requests</option>
          <option value="Pending">Pending</option>
          <option value="In Progress">In Progress</option>
          <option value="Completed">Completed</option>
        </select>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search requests..."
          class="w-full sm:w-auto border rounded-lg px-3 py-2"
        >
      </div>
    </div>

    <!-- Maintenance Requests Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
      <div
        v-for="request in filteredRequests"
        :key="request.id"
        class="bg-white p-4 lg:p-6 rounded-lg shadow"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-lg lg:text-xl font-semibold">{{ request.title }}</h3>
            <p class="text-sm text-gray-500">Room {{ request.roomNumber }}</p>
          </div>
          <span :class="getStatusClass(request.status)" class="px-2 py-1 rounded-full text-xs">
            {{ request.status }}
          </span>
        </div>
        <div class="space-y-2">
          <p class="text-sm text-gray-600">{{ request.description }}</p>
          <p><span class="font-medium">Date:</span> {{ request.date }}</p>
          <p><span class="font-medium">Priority:</span> {{ request.priority }}</p>
        </div>
        <div class="mt-4 flex gap-2">
          <button
            @click="viewRequest(request)"
            class="text-blue-600 hover:text-blue-800"
          >
            View
          </button>
          <button
            @click="updateStatus(request)"
            class="text-blue-600 hover:text-blue-800"
          >
            Update Status
          </button>
        </div>
      </div>
    </div>

    <!-- Submit Request Modal -->
    <div v-if="showAddRequestModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Submit Maintenance Request</h2>
        <form @submit.prevent="saveRequest" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Select Room</label>
            <select
              v-model="requestForm.roomNumber"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option v-for="room in rooms" :key="room.id" :value="room.number">
                Room {{ room.number }} - {{ room.type }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Issue Title</label>
            <input
              v-model="requestForm.title"
              type="text"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              v-model="requestForm.description"
              rows="3"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Priority</label>
            <select
              v-model="requestForm.priority"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option value="Low">Low</option>
              <option value="Medium">Medium</option>
              <option value="High">High</option>
              <option value="Urgent">Urgent</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Photos (Optional)</label>
            <input
              type="file"
              multiple
              accept="image/*"
              class="mt-1 block w-full"
              @change="handleFileUpload"
            >
          </div>
          <div class="flex justify-end gap-4 mt-6">
            <button
              type="button"
              @click="showAddRequestModal = false"
              class="px-4 py-2 border rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Submit Request
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Request Modal -->
    <div v-if="selectedRequest" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-2xl">
        <div class="flex justify-between items-start mb-4">
          <h2 class="text-xl font-bold">Maintenance Request Details</h2>
          <button
            @click="selectedRequest = null"
            class="text-gray-500 hover:text-gray-700"
          >
            ×
          </button>
        </div>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-medium text-gray-500">Request Information</h3>
              <p class="mt-1">Request #{{ selectedRequest.id }}</p>
              <p class="text-sm text-gray-500">Date: {{ selectedRequest.date }}</p>
              <p class="text-sm text-gray-500">Priority: {{ selectedRequest.priority }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Tenant Information</h3>
              <p class="mt-1">{{ selectedRequest.tenantName }}</p>
              <p class="text-sm text-gray-500">Room {{ selectedRequest.roomNumber }}</p>
            </div>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Issue Details</h3>
            <p class="mt-1 font-medium">{{ selectedRequest.title }}</p>
            <p class="mt-2 text-gray-600">{{ selectedRequest.description }}</p>
          </div>
          <div v-if="selectedRequest.photos && selectedRequest.photos.length > 0">
            <h3 class="text-sm font-medium text-gray-500">Photos</h3>
            <div class="mt-2 grid grid-cols-3 gap-4">
              <img
                v-for="(photo, index) in selectedRequest.photos"
                :key="index"
                :src="photo"
                class="w-full h-32 object-cover rounded-lg"
              >
            </div>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Status Updates</h3>
            <div class="mt-2 space-y-2">
              <div
                v-for="update in selectedRequest.statusUpdates"
                :key="update.id"
                class="flex items-start gap-2"
              >
                <div class="flex-1">
                  <p class="text-sm font-medium">{{ update.status }}</p>
                  <p class="text-sm text-gray-500">{{ update.date }}</p>
                  <p class="text-sm text-gray-600">{{ update.notes }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Update Status Modal -->
    <div v-if="showUpdateStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Update Request Status</h2>
        <form @submit.prevent="saveStatusUpdate" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">New Status</label>
            <select
              v-model="statusUpdateForm.status"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option value="Pending">Pending</option>
              <option value="In Progress">In Progress</option>
              <option value="Completed">Completed</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea
              v-model="statusUpdateForm.notes"
              rows="3"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            ></textarea>
          </div>
          <div class="flex justify-end gap-4 mt-6">
            <button
              type="button"
              @click="showUpdateStatusModal = false"
              class="px-4 py-2 border rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Update Status
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MaintenanceView',
  data() {
    return {
      requests: [
        {
          id: 1,
          tenantId: 1,
          tenantName: 'John Doe',
          tenantEmail: 'john@example.com',
          roomNumber: '101',
          title: 'Leaking Faucet',
          description: 'The kitchen faucet is leaking continuously. Please fix as soon as possible.',
          date: '2024-03-25',
          priority: 'Medium',
          status: 'Pending',
          photos: [],
          statusUpdates: [
            {
              id: 1,
              status: 'Pending',
              date: '2024-03-25',
              notes: 'Request submitted'
            }
          ]
        },
        {
          id: 2,
          tenantId: 2,
          tenantName: 'Jane Smith',
          tenantEmail: 'jane@example.com',
          roomNumber: '203',
          title: 'AC Repair',
          description: 'Air conditioning is not working properly. Room temperature is too high.',
          date: '2024-03-24',
          priority: 'High',
          status: 'In Progress',
          photos: [],
          statusUpdates: [
            {
              id: 1,
              status: 'Pending',
              date: '2024-03-24',
              notes: 'Request submitted'
            },
            {
              id: 2,
              status: 'In Progress',
              date: '2024-03-25',
              notes: 'Technician assigned'
            }
          ]
        },
        {
          id: 3,
          tenantId: 3,
          tenantName: 'Mike Johnson',
          tenantEmail: 'mike@example.com',
          roomNumber: '305',
          title: 'Light Bulb Replacement',
          description: 'Living room light bulb needs replacement.',
          date: '2024-03-23',
          priority: 'Low',
          status: 'Completed',
          photos: [],
          statusUpdates: [
            {
              id: 1,
              status: 'Pending',
              date: '2024-03-23',
              notes: 'Request submitted'
            },
            {
              id: 2,
              status: 'Completed',
              date: '2024-03-24',
              notes: 'Light bulb replaced'
            }
          ]
        }
      ],
      rooms: [
        { id: 1, number: '101', type: 'Standard' },
        { id: 2, number: '102', type: 'Deluxe' },
        { id: 3, number: '201', type: 'Suite' },
        // Add more rooms as needed
      ],
      showAddRequestModal: false,
      showUpdateStatusModal: false,
      selectedRequest: null,
      statusFilter: '',
      searchQuery: '',
      requestForm: {
        roomNumber: '',
        title: '',
        description: '',
        priority: 'Medium',
        photos: []
      },
      statusUpdateForm: {
        status: '',
        notes: ''
      }
    }
  },
  computed: {
    filteredRequests() {
      return this.requests.filter(request => {
        const matchesStatus = !this.statusFilter || request.status === this.statusFilter
        const matchesSearch = !this.searchQuery || 
          request.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          request.roomNumber.includes(this.searchQuery)
        return matchesStatus && matchesSearch
      })
    },
    totalRequests() {
      return this.requests.length
    },
    pendingRequests() {
      return this.requests.filter(request => request.status === 'Pending').length
    },
    inProgressRequests() {
      return this.requests.filter(request => request.status === 'In Progress').length
    },
    completedRequests() {
      return this.requests.filter(request => request.status === 'Completed').length
    }
  },
  methods: {
    getStatusClass(status) {
      const classes = {
        'Pending': 'bg-yellow-100 text-yellow-800',
        'In Progress': 'bg-blue-100 text-blue-800',
        'Completed': 'bg-green-100 text-green-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },
    handleFileUpload(event) {
      this.requestForm.photos = Array.from(event.target.files)
    },
    viewRequest(request) {
      this.selectedRequest = request
    },
    updateStatus(request) {
      this.selectedRequest = request
      this.statusUpdateForm = {
        status: request.status,
        notes: ''
      }
      this.showUpdateStatusModal = true
    },
    saveRequest() {
      const newRequest = {
        id: this.requests.length + 1,
        tenantId: 1, // This should be the actual tenant ID
        tenantName: 'John Doe', // This should be the actual tenant name
        tenantEmail: 'john@example.com', // This should be the actual tenant email
        roomNumber: this.requestForm.roomNumber,
        title: this.requestForm.title,
        description: this.requestForm.description,
        date: new Date().toISOString().split('T')[0],
        priority: this.requestForm.priority,
        status: 'Pending',
        photos: this.requestForm.photos,
        statusUpdates: [
          {
            id: 1,
            status: 'Pending',
            date: new Date().toISOString().split('T')[0],
            notes: 'Request submitted'
          }
        ]
      }
      this.requests.push(newRequest)
      this.showAddRequestModal = false
      this.requestForm = {
        roomNumber: '',
        title: '',
        description: '',
        priority: 'Medium',
        photos: []
      }
    },
    saveStatusUpdate() {
      const request = this.selectedRequest
      const newUpdate = {
        id: request.statusUpdates.length + 1,
        status: this.statusUpdateForm.status,
        date: new Date().toISOString().split('T')[0],
        notes: this.statusUpdateForm.notes
      }
      request.statusUpdates.push(newUpdate)
      request.status = this.statusUpdateForm.status
      this.showUpdateStatusModal = false
      this.statusUpdateForm = {
        status: '',
        notes: ''
      }
    }
  }
}
</script> 