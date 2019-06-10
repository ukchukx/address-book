<template>
  <form @submit.stop.prevent="submit()">
    <div class="form-group">
      <label>Name</label>
      <input type="text" v-model="form.name" class="form-control">
    </div>
    <div class="form-group">
      <label>Gender</label>
      <select v-model="form.gender" class="form-control">
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
    </div>
    <div class="form-group">
      <button :disabled="!formOk" type="submit">{{ buttonText }}</button>
    </div>
  </form>
</template>

<script>
export default {
  name: 'ContactForm',
  props: {
    initialData: {
      type: Object,
      required: true
    },
    buttonText: {
      type: String,
      required: true
    }
  },
  data() {
    const form = this.initialData ?
      { name: this.initialData.name, gender: this.initialData.gender } :
      { name: '', gender: '' };

    return {
      form
    };
  },
  computed: {
    formOk() {
      return !!this.form.name && ['male', 'female'].indexOf(this.form.gender) !== -1;
    }
  },
  methods: {
    submit() {
      this.$emit('form-submitted', this.form);
    },
    resetForm({ name, gender }) {
      this.form.name = name;
      this.form.gender = gender;
    }
  }
}
</script>
