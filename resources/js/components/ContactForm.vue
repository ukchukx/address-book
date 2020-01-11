<template>
  <form @submit.stop.prevent="submit()">
    <div class="form-group">
      <label>Name</label><br>
      <inline-input label-classes="h3" input-classes="form-control" placeholder="Name..." v-model="form.name" :emit-on-blur="!showButton" />
    </div>
    <div class="form-group">
      <label>Gender</label>
      <select v-model="form.gender" class="form-control">
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
    </div>
    <div v-if="showButton" class="form-group">
      <button :disabled="!formOk" type="submit">{{ buttonText }}</button>
    </div>
  </form>
</template>

<script>
import InlineInput from './InlineInput';

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
    form: {
      deep: true,
      handler(_) {
        if (!this.showButton) this.submit();
      }
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
    }
  }
}
</script>
