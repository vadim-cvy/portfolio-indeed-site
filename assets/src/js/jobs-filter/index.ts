import { createApp, ref } from "vue";
import Control from "./controls/Control";
import DropdownControl from "./controls/DropdownControl";
import Option from "./controls/Option";
import OptionsControl from "./controls/OptionsControl";
import Job from "./jobs/Job";
import JobDetailsBox from "./jobs/JobDetailsBox";

createApp({
  setup()
  {
    const matches = ref<Job[]>([])

    const jobDetailsBox = ref( JobDetailsBox )

    const controls = ref({
      searchTerm: new Control( 'searchTerm' ),
      location: new Control( 'location' ),

      secondary: [
        new DropdownControl( 'test', [
          new Option( 'bar', 'Bar' ),
          new Option( 'foo', 'Foo' ),
        ], 'Default label' ),
        new DropdownControl( 'test2', [
          new Option( 'bar', 'Bar' ),
          new Option( 'foo', 'Foo' ),
        ], 'Default label' ),
      ],

      sorting: new OptionsControl( 'sorting', [
        new Option( 'relevance', 'Relevance' ),
        new Option( 'date', 'Date' ),
      ])
    })

    const updateControlVal = ( control: Control, val: Control['val'] ) =>
    {
      control.change( val )

      // todo: if text control than wait a bit

      // todo: show loading

      // todo: handle errors
      // todo if request is sent - abort prev request
      findMatches().then( newMatches => matches.value = newMatches )
    }

    // todo: send real request
    // todo: handle errors
    const findMatches = () => new Promise<Job[]>( resolve =>
    {
      console.log( 'looking for matches' )

      const matches: Job[] = []

      for ( let i = 0; i <= 4; i++ )
      {
        const id = Math.floor(Math.random() * 1000)

        const fakeJob = new Job( id, `Fake ${id}` )

        matches.push( fakeJob )
      }

      resolve( matches )
    })

    return {
      matches,
      controls,
      jobDetailsBox,
      updateControlVal,
    }
  },
}).mount('#pjs-filter')