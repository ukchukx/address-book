<template>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-3 col-sm-12">
          <input v-model="searchText" type="text" class="form-control mb-3" placeholder="Search">
        </div>
      </div>
      <div class="scroll-card">
        <ul class="list-group">
          <ContactListItem 
            @delete-contact="deleteContact" 
            v-for="contact in filteredContacts" 
            :contact="contact"
            :key="contact.id" />
        </ul>
        <h4 v-if="!filteredContacts.length" class="text-muted text-center">No contacts</h4>
      </div>
    </div>
  </div>
</template>

<script>
import ContactListItem from './ContactListItem';

export default {
  name: 'ContactList',
  components: {
    ContactListItem
  },
  props: {
    contacts: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      searchText: ''
    };
  },
  computed: {
    searchTextLowerCase() {
      return this.searchText.toLowerCase();
    },
    filteredContacts() {
      return this.contacts
        .filter(({ name }) => !this.searchText.length || name.toLowerCase().includes(this.searchTextLowerCase));
    }
  },
  methods: {
    deleteContact(id) {
      if (! confirm('Are you sure?')) return;

      axios.delete(`/api/contacts/${id}`)
        .then(() => {
          this.$emit('contact-deleted', id);
        })
        .catch(({ response }) => {
          alert('Could not delete');
        });
    },
    selectContact(id) {
      this.$emit('contact-selected', id);
    }
  }
}
</script>
