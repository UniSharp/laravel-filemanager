export default {
  name: 'counterComponent',
  template: '#counter-template',
  data () {
    return {
      counter: ref(0)
    }
  },
  methods: {
    add () {
      counter.value++
    },
    substract () {
      counter.value--
    },
  },
}
