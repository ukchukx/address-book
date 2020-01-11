<template>
  <input 
    :class="inputClasses"
    v-if="editing" 
    :type="inputType" 
    :value="value" 
    :placeholder="placeholder"
    ref="inputEl"
    v-on:keyup.enter="handleEnter"
    @input="handleInput"
    @blur="handleBlur">

  <span :class="spanClasses" v-else @click="toggle()">{{ !!value ? value : placeholder }}</span>
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
    inputType() {
      return this.isNumber ? 'number' : 'text';
    },
    spanClasses() {
      return `${this.labelClasses} inline-input-label`;
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
    emitValue() {
      this.$emit('input', this.$refs.inputEl.value);
    }
  }
};
</script>
<style scoped>
.inline-input-label:hover {
  cursor: pointer
}
</style>
