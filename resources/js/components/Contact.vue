<template>
  <div v-if="isMobile && !contact"></div>
  <div v-else class="card">
    <div class="card-body">
      <h3 class="text-muted text-center" v-if="!contact">No contact selected</h3>
      <div v-else>
        <h4>{{ contact.name }}</h4>
        <hr>
        <contact-form
          ref="form"
          button-text="Update contact"
          :initial-data="form"
          @form-submitted="submit" />
        <hr>
        <notes :contact-id="contact.id" />
        <hr>
        <addresses :contact-id="contact.id" />
      </div>
    </div>
  </div>
</template>

<script>
import ContactForm from './ContactForm';
import Notes from './Notes';
import Addresses from './Addresses';
import MobileDetect from './mixins/MobileDetect';

export default {
  name: 'Contact',
  mixins: [MobileDetect],
  components: {
    ContactForm,
    Notes,
    Addresses
  },
  props: {
    contact: {
      type: Object
    }
  },
  data() {
    return {
      form: { name: '', gender: '' }
    };
  },
  watch: {
    contact: {
      deep: true,
      immediate: true,
      handler(c) {
        if (! c) return;

        const { name, gender } = c || { name: '', gender: '' };
        this.form = Object.assign(this.form, { name, gender });

        this.resetForm(this.form);
      }
    }
  },
  methods: {
    submit(form) {
      if (! confirm('Are you sure?')) return;

      axios.put(`/api/contacts/${this.contact.id}`, form)
        .then(({ data: { data } }) => {
          this.$emit('contact-updated', data);

          this.form.name = data.name;
          this.form.gender = data.gender;

          this.resetForm(this.form);
        })
        .catch(({ response }) => {
          alert('Could not create');
        });
    },
    resetForm() {
      if(! this.$refs.form) return;

      this.$refs.form.resetForm(this.form);
    }
  },
}
</script>
