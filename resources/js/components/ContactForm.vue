<template>
  <form @submit.stop.prevent="submit()">
    <div class="form-group">
      <label>Name</label><br>
      <InlineInput
        label-classes="h3" 
        input-classes="form-control" 
        placeholder="Name..." 
        @blur="inputBlurred"
        v-model="form.name" />
    </div>
    <div class="form-group">
      <label>Gender</label><br>
      <InlineInput
        label-classes="h3"
        input-classes="form-control"
        placeholder="Select gender"
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
    const form = this.initialData ?
      { name: this.initialData.name, gender: this.initialData.gender } :
      { name: '', gender: '' };

    return {
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
      return !!this.form.name && ['male', 'female'].includes(this.form.gender);
    },
    nameChanged() {
      return !!this.form.name && this.oldForm.name.trim() !== this.form.name.trim();
    },
    genderChanged() {
      return this.oldForm.gender.trim() !== this.form.gender.trim();
    }
  },
  watch: {
    'form.gender'(_) {
      if (!this.showButton) this.submit();
    }
  },
  methods: {
    submit() {
      if (!this.nameChanged && !this.genderChanged) return;

      this.oldForm = { ...this.form }; 

      this.$emit('form-submitted', this.form);
    },
    resetForm({ name, gender }) {
      this.form.name = name;
      this.form.gender = gender;
    },
    inputBlurred() {
      if (!this.showButton) this.submit();
    }
  }
}
</script>
