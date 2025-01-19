<script setup>
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const { user, campaigns } = usePage().props; // Access the authenticated user and campaigns

const createCampaign = () => {
    // Redirect to the create campaign page
    window.location.href = '/campaign/create'; // Adjust as needed
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flex container to align Create Campaign button to the right -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">Your Campaigns</h3>

                        <!-- Create Campaign Button on the right side -->
                        <button 
                            v-if="user" 
                            @click="createCampaign" 
                            class="px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 transition-all duration-300"
                        >
                            Create Campaign
                        </button>
                    </div>

                    <!-- If campaigns are available, display them -->
                    <div v-if="campaigns.length > 0">
                        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <li 
                                v-for="campaign in campaigns" 
                                :key="campaign.id" 
                                class="bg-gray-100 p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                            >
                                <div class="font-bold text-lg text-gray-800">{{ campaign.name }}</div>
                                <p class="text-gray-600">Status: <span class="font-medium text-blue-600">{{ campaign.status }}</span></p>
                                <p class="text-gray-600">Processed Contacts: <span class="font-medium text-green-600">{{ campaign.processed_contacts }}</span> / {{ campaign.total_contacts }}</p>
                            </li>
                        </ul>
                    </div>

                    <!-- If no campaigns, show a message -->
                    <div v-else class="mt-8 text-gray-700">
                        <p>You don't have any campaigns yet. Click the button above to create one!</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Container for the list */
ul {
    list-style-type: none;
    padding: 0;
}

/* Style for individual campaign items */
li {
    background-color: #f9fafb;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

/* Hover effect on campaign cards */
li:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Style for the Create Campaign button */
button {
    background-color: #3b82f6;
    color: white;
    font-size: 1rem;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 0.375rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #2563eb;
}

/* Grid layout for campaigns */
.grid {
    display: grid;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>
