<template>
  <div class="space-y-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 lg:p-8 text-white shadow-lg">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
          <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Welcome back, John!</h1>
          <p class="mt-2 text-blue-100">Here's what's happening with your properties today.</p>
        </div>
        <div class="flex items-center space-x-4">
          <button class="px-4 py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition-colors">
            <span class="text-sm font-medium">View Reports</span>
          </button>
          <button class="px-4 py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition-colors">
            <span class="text-sm font-medium">Settings</span>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Total Rooms</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">28</p>
          </div>
          <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span class="text-green-500 font-medium">+2.5%</span>
          <span class="ml-2 text-gray-500">from last month</span>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Occupied Rooms</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ occupiedRooms }}</p>
          </div>
          <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span class="text-green-500 font-medium">71.4%</span>
          <span class="ml-2 text-gray-500">occupancy rate</span>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Available Rooms</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ availableRooms }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span class="text-yellow-500 font-medium">21.4%</span>
          <span class="ml-2 text-gray-500">availability rate</span>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Under Maintenance</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ maintenanceRooms }}</p>
          </div>
          <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span class="text-red-500 font-medium">7.1%</span>
          <span class="ml-2 text-gray-500">maintenance rate</span>
        </div>
      </div>
    </div>

    <!-- Recent Activity and Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Upcoming Contract Expirations -->
      <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Upcoming Contract Expirations</h2>
            <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</button>
          </div>
        </div>
        <div class="p-6">
          <div class="space-y-6">
            <div v-for="contract in upcomingContracts" :key="contract.id" 
                 class="flex items-center justify-between group hover:bg-gray-50 p-4 rounded-lg transition-colors">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                  <span class="text-blue-600 font-medium text-sm">{{ contract.tenantName.split(' ').map(n => n[0]).join('') }}</span>
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ contract.tenantName }}</p>
                  <p class="text-sm text-gray-500">Room {{ contract.roomNumber }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <span class="text-sm text-red-600 font-medium">{{ contract.expiryDate }}</span>
                <button class="opacity-0 group-hover:opacity-100 text-blue-600 hover:text-blue-700">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Maintenance Requests -->
      <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Recent Maintenance Requests</h2>
            <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</button>
          </div>
        </div>
        <div class="p-6">
          <div class="space-y-6">
            <div v-for="request in maintenanceRequests" :key="request.id" 
                 class="flex items-center justify-between group hover:bg-gray-50 p-4 rounded-lg transition-colors">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ request.title }}</p>
                  <p class="text-sm text-gray-500">Room {{ request.roomNumber }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <span :class="getStatusClass(request.status)" 
                      class="px-3 py-1 rounded-full text-xs font-medium">
                  {{ request.status }}
                </span>
                <button class="opacity-0 group-hover:opacity-100 text-blue-600 hover:text-blue-700">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardView',
  data() {
    return {
      occupiedRooms: 20,
      availableRooms: 6,
      maintenanceRooms: 2,
      upcomingContracts: [
        { id: 1, tenantName: 'John Doe', roomNumber: '101', expiryDate: '2024-05-15' },
        { id: 2, tenantName: 'Jane Smith', roomNumber: '203', expiryDate: '2024-05-20' },
        { id: 3, tenantName: 'Mike Johnson', roomNumber: '305', expiryDate: '2024-05-25' }
      ],
      maintenanceRequests: [
        { id: 1, title: 'Leaking Faucet', roomNumber: '101', status: 'Pending' },
        { id: 2, title: 'AC Repair', roomNumber: '203', status: 'In Progress' },
        { id: 3, title: 'Light Bulb Replacement', roomNumber: '305', status: 'Completed' }
      ]
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
    }
  }
}
</script> 