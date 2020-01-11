<template>
  <input 
    :class="inputClasses"
    v-if="editing && (isText || isNumber)" 
    :type="type" 
    :value="value" 
    :placeholder="placeholder"
    ref="inputEl"
    v-on:keyup.enter="handleEnter"
    @input="handleInput"
    @blur="handleBlur">
  
  <select 
    :class="inputClasses"
    v-else-if="editing && isSelect"
    ref="inputEl" 
    :value="value"
    @change="handleChange"
    @blur="handleBlur">
    <option v-if="placeholder" disabled value>{{ placeholder }}</option>
    <option 
      :key="i"
      v-for="(o, i) in options" 
      :value="o.value">
      {{ o.label }}
    </option>
  </select>

  <span :class="spanClasses" v-else @click="toggle()">
    {{ label }}
    <span v-if="isSelect">&#9660;</span>
  </span>
</template>
<script>
export default {
  name: 'InlineInput',
  props: {
    value: {
      type: [Number, String],
      default: () => '' 
    },
    type: {
      type: String,
      default: () => 'text'
    },
    options: {
      type: Array,
      default: () => []
    },
    emitOnBlur: {
      type: Boolean,
      default: () => false
    },
    placeholder: {
      type: String,
      default: () => ''
    },
    labelClasses: {
      type: String,
      default: () => ''
    },
    inputClasses: {
      type: String,
      default: () => ''
    }
  },
  data() {
    return {
      editing: false
    };
  },
  computed: {
    isText() {
      return this.type === 'text';
    },
    isNumber() {
      return this.type === 'number';
    },
    isSelect() {
      return this.type === 'select';
    },
    inputType() {
      return this.isNumber ? 'number' : 'text';
    },
    spanClasses() {
      return `${this.labelClasses} inline-input-label`;
    },
    label() {
      if (this.isNumber) return this.value === '' ? this.placeholder : this.value;
      if (this.isText) return this.value ? this.value : this.placeholder;
      // Select
      return this.options.reduce((x, { label, value }) => this.value === value ? label : x, this.value);
    }
  },
  methods: {
  	toggle() {
      this.editing = !this.editing;
      
      if (this.editing) {
        this.$nextTick(() => {
          this.$refs.inputEl.focus();
        });
      }
    },
    handleEnter() {
      this.$refs.inputEl.blur();
    },
    handleBlur() {
      this.toggle();
      this.emitValue();
    },
    handleInput() {
      if (!this.emitOnBlur) this.emitValue();
    },
    handleChange() {
      this.emitValue();
    },
    emitValue() {
      this.$emit('input', this.isNumber ? parseFloat(this.$refs.inputEl.value) : this.$refs.inputEl.value);
    }
  }
};
</script>
<style scoped>
.inline-input-label:hover {
  cursor: pointer
}
</style>
