<script>
export default {
  name: 'MobileDetect',
  data() {
    return {
      displayProp: ''
    };
  },
  computed: {
    isMobile() {
      return this.displayProp === 'block';
    }
  },
  mounted() {
    this.updateDisplayProp();
  },
  methods: {
    updateDisplayProp() {
      const el = document.getElementById('mobileDetect');

      if (el) {
        this.displayProp = getComputedStyle(el, null).display;
      }

      setTimeout(() => this.updateDisplayProp(), 5000);
    }
  }
};
</script>
