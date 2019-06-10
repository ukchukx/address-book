<template>
  <div class="row justify-content-center">
    <div class="col-md-4">
      <create-contact @contact-created="handleContactCreated" />
      <contact-list
        @contact-deleted="handleContactDeleted"
        @contact-selected="handleContactSelected"
        :contacts="localContacts" />
    </div>
    <div class="col-md-8">
      <contact :contact="selectedContact" @contact-updated="handleContactUpdated" />
    </div>
  </div>
</template>

<script>
import CreateContact from './CreateContact';
import ContactList from './ContactList';
import Contact from './Contact';

export default {
  name: 'ContactView',
  components: {
    CreateContact,
    ContactList,
    Contact
  },
  props: {
    contacts: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      localContacts: this.contacts,
      selectedContact: null
    };
  },
  methods: {
    handleContactCreated(contact) {
      this.localContacts.push(contact);
    },
    handleContactDeleted(id) {
      this.localContacts = this.localContacts.filter(c => c.id !== id);
    },
    handleContactUpdated(contact) {
      this.localContacts = this.localContacts.map(c => c.id === contact.id ? contact : c);

      if ((this.selectedContact || { id: '' }).id === contact.id) {
        this.selectedContact = contact;
      }
    },
    handleContactSelected(id) {
      this.selectedContact = this.localContacts.find(c => c.id === id);
    }
  }
};
</script>
