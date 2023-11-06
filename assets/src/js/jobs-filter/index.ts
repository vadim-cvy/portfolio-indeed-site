import { createApp, ref } from "vue";
import Control from "./Control";
import ControlOption from "./ControlOption";

createApp({
  setup()
  {
    const
      searchTermVal = ref( '' ),
      locationVal = ref( '' )

    const secondaryControls = ref([
      new Control( 'test', 'Test', [
        new ControlOption( 'bar', 'Bar' ),
        new ControlOption( 'foo', 'Foo' ),
      ]),
      new Control( 'test2', 'Test 2', [
        new ControlOption( 'bar', 'Bar' ),
        new ControlOption( 'foo', 'Foo' ),
      ]),
    ])

    const sortingControl = ref(new Control( 'sorting', 'Sort by', [
      new ControlOption( 'relevance', 'Relevance' ),
      new ControlOption( 'date', 'Date' ),
    ]))

    const totalMatches = ref( 0 )

    const jobs = ref([
      {
        title: 'Test 1',
      },
      {
        title: 'Test 2',
      }
    ])

    const detailsCard = ref({
      isVisible: true,
      job: {
        title: 'Test',
      },
    })

    return {
      searchTermVal,
      locationVal,
      secondaryControls,
      sortingControl,
      totalMatches,
      jobs,
      detailsCard,
    }
  },

  methods: {

  },
}).mount('#pjs-filter')