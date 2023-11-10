import { createApp, ref, watch } from "vue";
import Control from "./controls/Control";
import DropdownControl from "./controls/DropdownControl";
import Option from "./controls/Option";
import OptionsControl from "./controls/OptionsControl";
import SearchTermControl from "./controls/SearchTermControl";
import Job from "./jobs/Job";
import JobDetailsBox from "./jobs/JobDetailsBox";
import axios, { AxiosRequestConfig } from 'axios'

createApp({
  setup()
  {
    const matches = ref({
      searchTerm: '',
      jobs: new Array<Job>(),
    })

    const controls = ref({
      searchTerm: new SearchTermControl(),
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

    const isLoading = ref( false )

    const jobDetailsBox = ref( JobDetailsBox )

    watch( isLoading, () => jobDetailsBox.value.toggle() )

    const updateControlVal = ( control: Control, val: Control['val'] ) =>
    {
      console.log( `Updating ${control.name} control value to "${val}"` )

      control.change( val )

      const isSearchTerm = control instanceof SearchTermControl

      if ( isSearchTerm && val === '' )
      {
        clearSubmissionData()

        matches.value.jobs = []

        return
      }

      const submitDelay = isSearchTerm ? 1200 : 800

      submit( submitDelay )
    }

    let
      activeSubmissionId: number | null = null,
      activeSubmissionTimer: ReturnType<typeof setTimeout> | null = null

    const submit = ( delay: number = 0, clearPrev = true ) =>
    {
      if ( clearPrev )
      {
        clearSubmissionData()

        while ( true )
        {
          const newSubmissionId = Math.floor( Math.random() * 10000 )

          if ( newSubmissionId !== activeSubmissionId )
          {
            activeSubmissionId = newSubmissionId

            break;
          }
        }

        console.log( `New submission id: ${activeSubmissionId}` )
      }

      const submissionId = activeSubmissionId

      if ( delay )
      {
        activeSubmissionConsoleLog( `Delaying for ${delay} ms` )

        activeSubmissionTimer = setTimeout( () => submit( 0, false ), delay )

        return
      }

      activeSubmissionConsoleLog( 'Processing' )

      isLoading.value = true

      matches.value.searchTerm = controls.value.searchTerm.val

      // todo: show loading (ui)

      // todo: handle errors
      findMatches()
      .then( jobs =>
      {
        activeSubmissionConsoleLog( 'Found matches:' )
        console.log( jobs )

        matches.value.jobs = jobs
      })
      .catch( errMsg => console.log(
        `Can't find matches for ${submissionId} submission. Error: ${errMsg}`
      ))
      .finally( () => isLoading.value = false )
    }

    const clearSubmissionData = () =>
    {
      console.log( 'Clearing active submission' );

      if ( activeSubmissionTimer )
      {
        activeSubmissionConsoleLog( 'Clearing timer' );

        clearTimeout( activeSubmissionTimer )
      }

      // todo if request is sent - abort prev request

      isLoading.value = false
    }

    // todo: send real request
    // todo: handle errors
    const findMatches = () => new Promise<Job[]>( ( resolve, reject ) =>
    {
      activeSubmissionConsoleLog( 'Looking for matches' )

      const controlVals: { [key: string]: string | number } = {}

      for ( const item of Object.values( controls.value ) )
      {
        if ( item instanceof Control )
        {
          controlVals[ item.name ] = item.val
        }
        else if ( Array.isArray( item ) )
        {
          item.map( control => controlVals[ control.name ] = control.val )
        }
      }

      const params: AxiosRequestConfig['params'] = {}

      for ( const key in controlVals )
      {
        const val = controlVals[ key ]

        if ( val !== '' )
        {
          const snakeCaseKey = key.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);

          params[ snakeCaseKey ] = val
        }
      }

      activeSubmissionConsoleLog( 'API request params:' )
      console.log( params )

      const submissionId = activeSubmissionId

      // todo: handle errors
      axios.get( '/wp-json/pjs/v1/frontend/jobs-filter/search', { params } )
      .then( response =>
      {
        // todo: handle errors sent by API (but maybe use status codes in API)

        const matches = response.data

console.log( matches )
throw new Error( 'This method is not completed, so I\'m throwing an error.' )
        // if ( submissionId !== activeSubmissionId )
        // {
        //   reject(
        //     'Request is not actual anymore.'
        //     + ` Active submission id is ${activeSubmissionId} while request was made for ${submissionId} submission`
        //   )
        // }
        // else
        // {
        //   resolve( matches )
        // }
      })
    })

    const activeSubmissionConsoleLog = ( msg: string ) =>
      console.log( `Submission ${activeSubmissionId}: ${msg}` )

    return {
      matches,
      controls,
      jobDetailsBox,
      isLoading,
      updateControlVal,
    }
  },
}).mount('#pjs-filter')