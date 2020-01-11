<template>
  <div class="row justify-content-center">
    <div class="col-sm-12 col-md-3">
      <create-contact @contact-created="handleContactCreated" />
    </div>
    <div class="col-sm-12 col-md-9">
      <contact-list
        @contact-deleted="handleContactDeleted"
        :contacts="localContacts" />
    </div>
  </div>
</template>

<script>
import CreateContact from './CreateContact';
import ContactList from './ContactList';

export default {
  name: 'Contacts',
  components: {
    CreateContact,
    ContactList
  },
  props: {
    contacts: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      localContacts: this.contacts
    };
  },
  methods: {
    handleContactCreated(contact) {
      this.localContacts.push(contact);
    },
    handleContactDeleted(id) {
      this.localContacts = this.localContacts.filter(c => c.id !== id);
    }
  }
};
</script>
