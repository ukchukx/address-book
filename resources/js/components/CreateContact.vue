<template>
  <div class="card mb-4">
    <div class="card-body">
      <contact-form
        ref="form"
        button-text="Create contact"
        show-button
        :initial-data="form"
        @form-submitted="submit" />
    </div>
  </div>
</template>

<script>
import ContactForm from './ContactForm';

export default {
  name: 'CreateContact',
  components: {
    ContactForm
  },
  data() {
    return {
      form: {
        name: '',
        gender: 'male'
      }
    };
  },
  methods: {
    submit(form) {
      axios.post('/api/contacts', form)
        .then(({ data: { data } }) => {
          this.$emit('contact-created', data);

          this.$refs.form.resetForm(this.form);
        })
        .catch(({ response }) => {
          alert('Could not create');
        });
    }
  }
}
</script>
