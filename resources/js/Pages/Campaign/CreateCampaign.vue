<template>
    <Head title="Create Campaign" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Campaign</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Display Errors -->
                        <div v-if="errors.length > 0" class="alert alert-danger mb-4">
                            <ul>
                                <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                            </ul>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submitForm" class="space-y-6" enctype="multipart/form-data">
                            <!-- Campaign Name Input -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Campaign Name</label>
                                <input
                                    v-model="form.name"
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                    placeholder="Enter campaign name"
                                />
                            </div>

                            <!-- CSV File Input -->
                            <div class="mb-4">
                                <label for="csv_file" class="block text-sm font-medium text-gray-700">Upload CSV File</label>
                                <input
                                    type="file"
                                    @change="handleFileUpload"
                                    id="csv_file"
                                    class="mt-1 block w-full text-sm text-gray-700 file:border file:border-gray-300 file:py-2 file:px-4 file:rounded-md file:text-sm file:bg-gray-100 hover:file:bg-gray-200"
                                    required
                                />
                                <span v-if="csvError" class="text-red-600 text-sm mt-1 block">{{ csvError }}</span>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center px-6 py-2 text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Create Campaign
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    csv_file: null,
});

const csvError = ref('');
const errors = ref([]);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file && file.type !== 'text/csv') {
        csvError.value = 'Please upload a valid CSV file.';
        form.csv_file = null;
    } else {
        csvError.value = '';
        form.csv_file = file;
    }
};

const submitForm = () => {
    if (!form.csv_file) {
        csvError.value = 'Please upload a CSV file.';
        return;
    }

    // Prepare form data for file upload
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('csv_file', form.csv_file);

    // Use the useForm() hook to submit the form
    form.post(route('campaign.store'), {
        data: formData,
        onError: (err) => {
            errors.value = err;  // Store errors in the 'errors' array
        },
        onSuccess: () => {
            form.reset(); // Reset form after success
            csvError.value = '';
            // Optionally show success message or redirect
        }
    });
};
</script>
