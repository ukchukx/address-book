<template>
  <li class="list-group-item">
    <div class="row">
      <div class="col-md-9">
        <h5>
          <a class="text-dark" :href="contactUrl">{{ contact.name }}</a>
        </h5>
        <small>{{ contact.gender }}</small>
      </div>
      <div class="col-md-3 text-right">
        <div class="btn-group">
          <a class="btn btn-sm btn-outline-secondary" :href="contactUrl">View</a>
          <button class="btn btn-sm btn-outline-danger" @click="deleteContact()">Delete</button>
        </div>
      </div>
    </div>
  </li>
</template>
<script>
export default {
  name: 'ContactListItem',
  props: {
    contact: {
      required: true,
      type: Object
    }
  },
  computed: {
    contactUrl() {
      return `/contacts/${this.contact.id}`;
    }
  },
  methods: {
    deleteContact() {
      this.$emit('delete-contact', this.contact.id);
    }
  }
};
</script>
