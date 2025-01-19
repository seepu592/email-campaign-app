<template>
  <div class="container">
    <h1>All Campaigns</h1>
    
    <div v-if="campaigns.length === 0">
      <p>No campaigns found.</p>
    </div>
    
    <ul class="campaign-list">
      <li v-for="campaign in campaigns" :key="campaign.id" class="campaign-item">
        <a @click.prevent="goToCampaignProgressUpdate(campaign.id)" class="campaign-link">
          <div class="campaign-details">
            <h2 class="campaign-name">{{ campaign.name }}</h2>
            <p class="campaign-status">
              Processed: {{ campaign.processed_contacts }} / {{ campaign.total_contacts }}
            </p>
          </div>
        </a>

        <!-- Buttons -->
        <div class="buttons">
          <!-- Back button -->
          <a @click.prevent="goToCampaignProgress(campaign.id)" class="btn-back">Back</a>
          
          <!-- Check button to trigger the progress update -->
          <a @click.prevent="goToCampaignProgressUpdate(campaign.id)" class="btn-check">
            Check Progress
          </a>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';  // Correct import

export default {
  props: {
    campaigns: Array,
  },
  methods: {
    goToCampaignProgress(id) {
      Inertia.visit('/campaign/create'); 
    },
    goToCampaignProgressUpdate(id) {
      Inertia.visit(`/campaign/${id}/progress/update`);
    }
  }
};
</script>



<style scoped>
.container {
  width: 80%;
  margin: 0 auto;
  padding-top: 20px;
}

h1 {
  text-align: center;
  margin-bottom: 20px;
}

.campaign-list {
  list-style-type: none;
  padding: 0;
}

.campaign-item {
  background-color: #f4f4f4;
  padding: 15px;
  margin: 10px 0;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.campaign-link {
  text-decoration: none;
  color: inherit;
}

.campaign-details {
  display: flex;
  flex-direction: column;
}

.campaign-name {
  font-size: 1.2em;
  font-weight: bold;
  margin-bottom: 10px;
}

.campaign-status {
  font-size: 1em;
  color: #555;
}

.buttons {
  margin-top: 10px;
  display: flex;
  gap: 10px;
}

.btn-back,
.btn-check {
  padding: 10px 15px;
  font-size: 14px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-back {
  background-color: #f0f0f0;
  border: 1px solid #ccc;
}

.btn-check {
  background-color: #28a745;
  color: white;
  border: 1px solid #218838;
}

.btn-back:hover,
.btn-check:hover {
  opacity: 0.8;
}
</style>
