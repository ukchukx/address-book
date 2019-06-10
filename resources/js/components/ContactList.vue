<template>
  <div class="card">
    <div class="card-body">
      <ul class="list-group">
        <li class="list-group-item" v-for="contact in contacts" :key="contact.id">
          <div class="row">
            <div class="col-md-9">
              <h5>{{ contact.name }}</h5>
              <small>{{ contact.gender }}</small>
            </div>
            <div class="col-md-3 text-right">
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-danger" @click="deleteContact(contact.id)">Delete</button>
                <button class="btn btn-sm btn-outline-secondary" @click="selectContact(contact.id)">View</button>
              </div>
            </div>
          </div>
        </li>
      </ul>
      <h4 v-if="!contacts.length" class="text-muted text-center">No contacts</h4>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContactList',
  props: {
    contacts: {
      type: Array,
      required: true
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
