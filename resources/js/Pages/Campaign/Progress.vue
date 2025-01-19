<template>
  <div class="container">
    <h1 class="campaign-title">Campaign: {{ campaign.name }}</h1>
    <p class="status"><strong>Status:</strong> {{ campaign.status }}</p>
    <p class="processed">
      <strong>Processed Contacts:</strong> {{ processedContacts }} / {{ campaign.total_contacts }}
    </p>
    <p class="remaining">
      <strong>Remaining Contacts:</strong> {{ remainingContacts }}
    </p>

    <h2 class="contacts-title">Contacts:</h2>
    <ul class="contact-list">
      <li v-for="contact in campaign.contacts" :key="contact.id" class="contact-item">
        <p class="contact-info">
          <strong>{{ contact.name }}</strong> - {{ contact.email }}
        </p>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    campaign: Object,
    processedContacts: Number,
    remainingContacts: Number,
  },
};
</script>

<style scoped>
.container {
  width: 80%;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Arial', sans-serif;
}

.campaign-title {
  font-size: 2em;
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}

.status, .processed, .remaining {
  font-size: 1.2em;
  margin: 10px 0;
  color: #555;
}

.status strong, .processed strong, .remaining strong {
  color: #333;
}

.contacts-title {
  font-size: 1.5em;
  margin-top: 30px;
  color: #333;
}

.contact-list {
  list-style-type: none;
  padding: 0;
  margin-top: 10px;
}

.contact-item {
  background-color: #f9f9f9;
  padding: 10px;
  margin: 10px 0;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.contact-info {
  font-size: 1.1em;
  color: #333;
}

.contact-info strong {
  color: #007bff;
}

.contact-list .contact-item:hover {
  background-color: #e9e9e9;
  cursor: pointer;
}

.container p, .container h2 {
  margin: 10px 0;
}

.container ul {
  padding-left: 20px;
}
</style>
