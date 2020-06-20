<template>
  <form @submit.stop.prevent="submit()">
    <div class="form-group">
      <label>Name</label>
      <br>
      <InlineInput label-classes="h3" input-classes="form-control" placeholder="Name..." v-model="form.name" />
    </div>
    <div class="form-group">
      <label>Gender</label>
      <br>
      <InlineInput
        label-classes="h3"
        input-classes="form-control"
        placeholder="Gender..."
        type="select"
        :options="genderOptions"
        v-model="form.gender" />
    </div>
    <div v-if="showButton" class="form-group">
      <button :disabled="!formOk" type="submit">{{ buttonText }}</button>
    </div>
  </form>
</template>

<script>
import InlineInput from 'vue-inline-input';

export default {
  name: 'ContactForm',
  components: {
    InlineInput
  },
  props: {
    initialData: {
      type: Object,
      required: true
    },
    buttonText: {
      type: String,
      required: true
    },
    showButton: {
      type: Boolean,
      default: () => false
    }
  },
  data() {
    const { name = '', gender = '' } = this.initialData, 
      form = { name, gender };

    return {
      nameTimeout: 0,
      genderOptions: [
        { label: 'Male', value: 'male' },
        { label: 'Female', value: 'female' }
      ],
      oldForm: { ...form },
      form
    };
  },
  computed: {
    formOk() {
      return this.nameOk && this.genderOk;
    },
    genderOk() {
      return ['male', 'female'].includes(this.form.gender);
    },
    nameOk() {
      return !!this.form.name;
    },
    genderChanged() {
      return this.oldForm.gender.trim() !== this.form.gender.trim();
    },
    nameChanged() {
      return this.oldForm.name.trim() !== this.form.name.trim();
    }
  },
  watch: {
    'form.gender'(_) {
      this.submit();
    },
    'form.name'(_) {
      if (this.nameTimeout) clearTimeout(this.nameTimeout);

      this.nameTimeout = setTimeout(() => this.submit(), 500);
    }
  },
  methods: {
    submit() {
      if (!this.nameChanged && !this.genderChanged) return;

      this.oldForm = { ...this.form };

      this.$emit('form-submitted', this.oldForm);
    },
    resetForm({ name, gender }) {
      this.form.name = name;
      this.form.gender = gender;
    }
  }
}
</script>
