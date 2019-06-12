<template>
  <div class="row justify-content-center">
    <div class="col-md-4 col-sm-12">
      <div v-show="!contactSelectedOnMobile">
        <create-contact @contact-created="handleContactCreated" />
        <contact-list
          @contact-deleted="handleContactDeleted"
          @contact-selected="handleContactSelected"
          :contacts="localContacts" />
      </div>
    </div>
    <div class="col-md-8 col-sm-12">
      <button v-if="contactSelectedOnMobile" class="btn btn-outline-secondary mb-3" @click="resetSelectedContact()">Back</button>
      <contact :contact="selectedContact" @contact-updated="handleContactUpdated" />
    </div>
    <span id="mobileDetect" class="d-md-none d-lg-none d-sm-inline d-xs-inline"></span>
  </div>
</template>

<script>
import CreateContact from './CreateContact';
import ContactList from './ContactList';
import Contact from './Contact';
import MobileDetect from './mixins/MobileDetect';

export default {
  name: 'ContactView',
  mixins: [MobileDetect],
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
  computed: {
    contactSelectedOnMobile() {
      return this.isMobile && !!this.selectedContact;
    }
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
    },
    resetSelectedContact() {
      this.selectedContact = null;
    }
  }
};
</script>
