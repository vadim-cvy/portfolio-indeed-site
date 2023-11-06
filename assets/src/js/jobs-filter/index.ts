import { createApp, ref } from "vue";
import Control from "./Control";
import ControlOption from "./ControlOption";
import Job from "./Job";
import JobDetailsBox from "./JobDetailsBox";

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

    const jobs = ref([
      new Job( 'Test 1' ),
      new Job( 'Test 2' ),
    ])

    const jobDetailsBox = ref( JobDetailsBox )

    return {
      searchTermVal,
      locationVal,
      secondaryControls,
      sortingControl,
      jobs,
      jobDetailsBox
    }
  },
}).mount('#pjs-filter')