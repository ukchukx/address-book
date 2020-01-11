<template>
  <div class="row justify-content-center">
    <div class="col-sm-12">
      <a href="/contacts" class="mb-2">&larr; Back</a>
    </div>
    <div class="col-sm-12">
      <div class="row">
        <div class="col-sm-12 col-md-4 mb-3">
          <div class="card">
            <div class="card-body">
              <hr>
              <contact-form
                ref="form"
                button-text="Update contact"
                :initial-data="form"
                @form-submitted="submit" />
              <hr>
              <addresses :contact-id="contact.id" />
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-8">
          <div class="card">
            <div class="card-body">
              <notes :contact-id="contact.id" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ContactForm from './ContactForm';
import Notes from './Notes';
import Addresses from './Addresses';

export default {
  name: 'Contact',
  components: {
    ContactForm,
    Notes,
    Addresses
  },
  props: {
    contact: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      form: { name: this.contact.name, gender: this.contact.gender },
      localContact: this.contact
    };
  },
  methods: {
    submit(form) {
      axios.put(`/api/contacts/${this.localContact.id}`, form)
        .then(({ data: { data } }) => {
          console.log(data);
          this.localContact = data;

          this.form.name = data.name;
          this.form.gender = data.gender;

          this.resetForm(this.form);
        })
        .catch(({ response }) => {
          alert('Could not update');
        });
    },
    resetForm() {
      if(! this.$refs.form) return;

      this.$refs.form.resetForm(this.form);
    }
  },
}
</script>
