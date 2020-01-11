<template>
  <div>
    <p><span class="h4">Addresses</span> &emsp; <button @click="view()">New</button></p>
    <p v-if="loadingAddresses"><i>Loading...</i></p>
    <div class="scroll-card">
      <ul v-if="addresses.length" class="list-group">
        <li v-for="(address, i) in addresses" :key="address.id" class="list-group-item d-flex justify-content-between align-items-center">
          <span class="badge badge-secondary">{{ address.key | keyLabel }}</span>
          {{ address.value }}
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary" @click.prevent.stop="view(i)">Edit</button>
            <button type="button" class="btn btn-outline-danger" @click.prevent.stop="deleteAddress(i)">Delete</button>
          </div>
        </li>
      </ul>
      <h2 class="text-muted text-center" v-else>No addresses</h2>
    </div>

    <div ref="modal" class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group row">
                <label class="control-label col-sm-12">Type</label><br>
                <div class="col-sm-12">
                  <inline-input
                    label-classes="h3"
                    input-classes="form-control"
                    type="select"
                    :options="keyOptions"
                    v-model="address.key" />
                </div>
              </div>
              <div class="form-group row">
                <label class="control-label col-sm-12">Value</label><br>
                <div class="col-sm-12">
                  <inline-input 
                    label-classes="h3"
                    input-classes="form-control"
                    :placeholder="placeholder" 
                    v-model="address.value" />
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              :disabled="!formOk"
              @click.stop.prevent="save()"
              type="button"
              class="btn btn-secondary"
            >Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InlineInput from './InlineInput';

export default {
  name: 'Addresses',
  components: {
    InlineInput
  },
  props: {
    contactId: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      keyOptions: [
        { label: 'Phone', value: 'phone' },
        { label: 'Email', value: 'email' },
        { label: 'Address', value: 'physical' }
      ],
      addresses: [],
      loadingAddresses: false,
      currAddress: -1,
      address: {
        id: '',
        contact_id: this.contactId,
        key: 'phone',
        value: ''
      }
    };
  },
  computed: {
    modalTitle() {
      return !!this.address.id ? 'Update contact detail' : 'Add contact detail';
    },
    placeholder() {
      if (this.address.key === 'phone') return 'Enter phone number';

      if (this.address.key === 'email') return 'Enter email address';

      if (this.address.key === 'physical') return 'Enter address';
    },
    keyOk() {
      return ['email', 'phone', 'physical'].includes(this.address.key);
    },
    valueOk() {
      const { address: { id, value, key }, addresses, currAddress } = this;

      return !!value &&
        (currAddress === -1 ? true : addresses[currAddress].value !== value.trim()) && // did the value change?
        addresses.filter(a => a.key === key).every(a => a.value !== value); // ensure it's not a duplicate
    },
    formatOk() {
      if (this.address.key === 'email') return /^\w+\.*\w*@\w+\.\w+/.test(this.address.value);

      if (this.address.key === 'phone') return /^[+]?\d+$/.test(this.address.value);

      if (this.address.key === 'physical') return !!this.address.value;
    },
    formOk() {
      return this.keyOk && this.valueOk && this.formatOk;
    }
  },
  watch: {
    contactId: {
      immediate: true,
      handler() {
        this.address.contact_id = this.contactId;
        this.fetchAddresses();
      }
    }
  },
  filters: {
    keyLabel(key) {
      if (key === 'physical') return 'Address';

      return `${key[0].toUpperCase()}${key.substr(1)}`;
    }
  },
  methods: {
    fetchAddresses() {
      this.addresses = [];
      this.loadingAddresses = true;

      axios.get(`/api/contacts/${this.contactId}/addresses`)
        .then(({ data: { data } }) => {
          this.addresses = data;
          this.loadingAddresses = false;
        })
        .catch(() => {
          this.loadingAddresses = false;
        });
    },
    deleteAddress(index) {
      if (!confirm('Are you sure?')) return;

      axios.delete(`/api/addresses/${this.addresses[index].id}`)
        .then(() => {
          this.addresses.splice(index, 1);
        })
        .catch(() => {
          alert('Could not delete');
        });
    },
    save() {
      this.address.key = this.address.key.trim();
      this.address.value = this.address.value.trim();

      if (this.address.id) {
        axios.put(`/api/addresses/${this.address.id}`, this.address)
          .then(({ data: { data } }) => {
            $(this.$refs.modal).modal('hide');

            this.addresses[this.currAddress] = data;

            this.resetAddress();
          })
          .catch(() => {
            alert('Could not update');
          });
      } else {
        axios.post('/api/addresses', this.address)
          .then(({ data: { data } }) => {
            this.addresses.push(data);

            $(this.$refs.modal).modal('hide');
          })
          .catch(() => {
            alert('Could not create');
          });
      }
    },
    view(index = -1) {
      this.currAddress = index;

      if (index !== -1) {
        this.address = { ...this.addresses[this.currAddress] };
      } else {
        this.resetAddress();
      }

      $(this.$refs.modal).modal('show');
    },
    resetAddress() {
      this.address.id = '';
      this.address.value = '';
      this.address.key = 'phone';
      this.currAddress = -1;
    }
  }
}
</script>
