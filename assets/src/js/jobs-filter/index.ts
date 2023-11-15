import { createApp, ref, watch } from "vue";
import Control from "./controls/Control";
import DropdownControl from "./controls/DropdownControl";
import Option from "./controls/Option";
import OptionsControl from "./controls/OptionsControl";
import SearchTermControl from "./controls/SearchTermControl";
import Job from "./jobs/Job";
import JobDetailsBox from "./jobs/JobDetailsBox";
import axios, { AxiosError, AxiosRequestConfig } from 'axios'

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

      // todo: uncomment
      // const submitDelay = isSearchTerm ? 1200 : 800
      const submitDelay = isSearchTerm ? 500 : 100

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

      const submissionId = Number( activeSubmissionId )

      if ( delay )
      {
        submissionLog( submissionId, `Delaying for ${delay} ms` )

        activeSubmissionTimer = setTimeout( () => submit( 0, false ), delay )

        return
      }

      submissionLog( submissionId, 'Processing' )

      isLoading.value = true

      matches.value.searchTerm = controls.value.searchTerm.val

      // todo: show loading (ui)

      // todo: handle errors
      findMatches()
      .then( jobs =>
      {
        if ( submissionId !== activeSubmissionId )
        {
          submissionLog( submissionId, 'Ignoring submission results as submission is not actual anymore.', 'debug' )
        }
        else
        {
          submissionLog( submissionId, 'Found matches:' )
          console.log( jobs )

          matches.value.jobs = jobs
        }
      })
      .finally( () => isLoading.value = false )
    }

    const clearSubmissionData = () =>
    {
      console.log( 'Clearing active submission' );

      if ( activeSubmissionTimer )
      {
        submissionLog( Number( activeSubmissionId ), 'Clearing timer' );

        clearTimeout( activeSubmissionTimer )
      }

      // todo if request is sent - abort prev request

      isLoading.value = false

      activeSubmissionId = null
    }

    // todo: send real request
    // todo: handle errors
    const findMatches = () => new Promise<Job[]>( ( resolve, reject ) =>
    {
      const submissionId = Number( activeSubmissionId )

      submissionLog( submissionId, 'Looking for matches' )

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

      const params: AxiosRequestConfig['params'] = {
        country_id: pjsJobsFilter.countryId,
      }

      for ( const key in controlVals )
      {
        const val = controlVals[ key ]

        if ( val !== '' )
        {
          const snakeCaseKey = key.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);

          params[ snakeCaseKey ] = val
        }
      }

      submissionLog( submissionId, 'API request params:', 'debug' )
      console.debug( params )

      // todo: handle errors
      axios.get( '/wp-json/pjs/v1/frontend/jobs-filter/search', { params } )
      .then( response =>
      {
        const dbQueriesTotalTime =
          response.data.debug.db
          .map( ( query: { time: number } ) => query.time )
          .reduce( ( accamulator: number, curent: number ) => accamulator + curent )

        submissionLog(
          submissionId,
          `API request DB queries total time: ${dbQueriesTotalTime.toFixed(3)}`,
          'debug'
        )

        if ( dbQueriesTotalTime > 0.2 )
        {
          submissionLog( submissionId, 'DB queries total time is more than 200ms', 'warn' )
        }

        if ( response.data.status !== 'success' )
        {
          // todo: handle
        }

        const matches = response.data.matches as Job[]

        resolve( matches )
      })
      .catch( ( e: AxiosError ) =>
      {
        swal.fire(
          'Something goes wrong',
          'Please, try to reload the page.',
          'warning'
        )

        submissionLog( submissionId, 'Axios error: ' );
        console.log( e )
      })
    })

    const submissionLog = (
      submissionId: number,
      msg: string,
      type: 'log' | 'debug' | 'warn' = 'log'
    ) =>
    {
      let prefix = 'Submission ' + submissionId

      if ( submissionId === activeSubmissionId )
      {
        prefix += ' (active)'
      }

      prefix += ': '

      console[ type ]( prefix + msg )
    }

    return {
      matches,
      controls,
      jobDetailsBox,
      isLoading,
      updateControlVal,
    }
  },
}).mount('#pjs-filter')