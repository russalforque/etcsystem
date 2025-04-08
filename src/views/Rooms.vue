<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h1 class="text-2xl font-bold">Room Management</h1>
      <button
        @click="showAddRoomModal = true"
        class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
      >
        Add New Room
      </button>
    </div>

    <!-- Room Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="flex flex-col sm:flex-row gap-4">
        <select
          v-model="statusFilter"
          class="w-full sm:w-auto border rounded-lg px-3 py-2"
        >
          <option value="">All Status</option>
          <option value="Available">Available</option>
          <option value="Occupied">Occupied</option>
          <option value="Under Maintenance">Under Maintenance</option>
        </select>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search rooms..."
          class="w-full sm:w-auto border rounded-lg px-3 py-2"
        >
      </div>
    </div>

    <!-- Rooms Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
      <div
        v-for="room in filteredRooms"
        :key="room.id"
        class="bg-white p-4 lg:p-6 rounded-lg shadow"
      >
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-lg lg:text-xl font-semibold">Room {{ room.number }}</h3>
          <span :class="getStatusClass(room.status)" class="px-2 py-1 rounded-full text-xs">
            {{ room.status }}
          </span>
        </div>
        <div class="space-y-2">
          <p><span class="font-medium">Floor:</span> {{ room.floor }}</p>
          <p><span class="font-medium">Type:</span> {{ room.type }}</p>
          <p><span class="font-medium">Rent:</span> ${{ room.rent }}/month</p>
          <p v-if="room.tenant" class="text-sm text-gray-600">
            Tenant: {{ room.tenant }}
          </p>
        </div>
        <div class="mt-4 flex gap-2">
          <button
            @click="editRoom(room)"
            class="text-blue-600 hover:text-blue-800"
          >
            Edit
          </button>
          <button
            @click="deleteRoom(room.id)"
            class="text-red-600 hover:text-red-800"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Room Modal -->
    <div v-if="showAddRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ editingRoom ? 'Edit Room' : 'Add New Room' }}</h2>
        <form @submit.prevent="saveRoom" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Room Number</label>
            <input
              v-model="roomForm.number"
              type="text"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Floor</label>
            <input
              v-model="roomForm.floor"
              type="number"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Type</label>
            <select
              v-model="roomForm.type"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option value="Standard">Standard</option>
              <option value="Deluxe">Deluxe</option>
              <option value="Suite">Suite</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Monthly Rent</label>
            <input
              v-model="roomForm.rent"
              type="number"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select
              v-model="roomForm.status"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
              required
            >
              <option value="Available">Available</option>
              <option value="Occupied">Occupied</option>
              <option value="Under Maintenance">Under Maintenance</option>
            </select>
          </div>
          <div class="flex justify-end gap-4 mt-6">
            <button
              type="button"
              @click="showAddRoomModal = false"
              class="px-4 py-2 border rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              {{ editingRoom ? 'Save Changes' : 'Add Room' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RoomsView',
  data() {
    return {
      rooms: [
        { id: 1, number: '101', floor: '1st', type: 'Standard', rent: 800, status: 'Occupied', tenant: 'John Doe' },
        { id: 2, number: '102', floor: '1st', type: 'Deluxe', rent: 1000, status: 'Available' },
        { id: 3, number: '201', floor: '2nd', type: 'Suite', rent: 1200, status: 'Under Maintenance' },
        // Add more rooms as needed
      ],
      showAddRoomModal: false,
      editingRoom: null,
      statusFilter: '',
      searchQuery: '',
      roomForm: {
        number: '',
        floor: '',
        type: 'Standard',
        rent: '',
        status: 'Available'
      }
    }
  },
  computed: {
    filteredRooms() {
      return this.rooms.filter(room => {
        const matchesStatus = !this.statusFilter || room.status === this.statusFilter
        const matchesSearch = !this.searchQuery || 
          room.number.includes(this.searchQuery) ||
          (room.tenant && room.tenant.toLowerCase().includes(this.searchQuery.toLowerCase()))
        return matchesStatus && matchesSearch
      })
    }
  },
  methods: {
    getStatusClass(status) {
      const classes = {
        'Available': 'bg-green-100 text-green-800',
        'Occupied': 'bg-blue-100 text-blue-800',
        'Under Maintenance': 'bg-yellow-100 text-yellow-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },
    editRoom(room) {
      this.editingRoom = room
      this.roomForm = { ...room }
      this.showAddRoomModal = true
    },
    deleteRoom(roomId) {
      if (confirm('Are you sure you want to delete this room?')) {
        this.rooms = this.rooms.filter(room => room.id !== roomId)
      }
    },
    saveRoom() {
      if (this.editingRoom) {
        const index = this.rooms.findIndex(room => room.id === this.editingRoom.id)
        this.rooms[index] = { ...this.editingRoom, ...this.roomForm }
      } else {
        const newRoom = {
          id: this.rooms.length + 1,
          ...this.roomForm
        }
        this.rooms.push(newRoom)
      }
      this.showAddRoomModal = false
      this.editingRoom = null
      this.roomForm = {
        number: '',
        floor: '',
        type: 'Standard',
        rent: '',
        status: 'Available'
      }
    }
  }
}
</script> 