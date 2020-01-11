<template>
  <input 
    v-if="editing" 
    :type="inputType" 
    v-model="internalValue" 
    :placeholder="placeholder"
    ref="inputEl"
    v-on:keyup.enter="handleEnter()"
    @blur="handleBlur()">

  <span v-else @click="toggle()">{{ !!internalValue ? internalValue : placeholder }}</span>
</template>
<script>
export default {
  name: 'InlineInput',
  props: {
    value: {
      type: [Number, String],
      default: () => '' 
    },
    isNumber: {
      type: Boolean,
      default: () => false
    },
    emitOnBlur: {
      type: Boolean,
      default: () => false
    },
    placeholder: {
      type: String,
      default: () => ''
    }
  },
  data() {
    return {
      internalValue: this.value,
      editing: false
    };
  },
  computed: {
    inputType() {
      return this.isNumber ? 'number' : 'text';
    }
  },
  watch: {
    internalValue() {
      if (!this.emitOnBlur) this.emitValue();
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
    emitValue() {
      this.$emit('input', this.internalValue);
    }
  }
};
</script>
