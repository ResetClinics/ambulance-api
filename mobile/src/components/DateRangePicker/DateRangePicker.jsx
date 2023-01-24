/* eslint-disable */
import React, { useState } from 'react'
import { CalendarList } from 'react-native-calendars'

const XDate = require('xdate')

export const DateRangePicker = (props) => {
  const [state, setState] = useState({ isFromDatePicked: false, isToDatePicked: false, markedDates: {} })

  const componentDidMount = () => setupInitialRange()

  const onDayPress = (day) => {
    if (!state.isFromDatePicked || (state.isFromDatePicked && state.isToDatePicked)) {
      setupStartMarker(day)
    } else if (!state.isToDatePicked) {
      const markedDates = { ...state.markedDates }
      const [mMarkedDates, range] = setupMarkedDates(state.fromDate, day.dateString, markedDates)
      if (range >= 0) {
        setState({ isFromDatePicked: true, isToDatePicked: true, markedDates: mMarkedDates })
        props.onSuccess(state.fromDate, day.dateString)
      } else {
        setupStartMarker(day)
      }
    }
  }

  const setupStartMarker = (day) => {
    const markedDates = { [day.dateString]: { startingDay: true, color: props.theme.markColor, textColor: props.theme.markTextColor } }
    setState({
      isFromDatePicked: true, isToDatePicked: false, fromDate: day.dateString, markedDates
    })
  }

  const setupMarkedDates = (fromDate, toDate, markedDates) => {
    const mFromDate = new XDate(fromDate)
    const mToDate = new XDate(toDate)
    const range = mFromDate.diffDays(mToDate)
    if (range >= 0) {
      if (range === 0) {
        markedDates = { [toDate]: { color: props.theme.markColor, textColor: props.theme.markTextColor } }
      } else {
        for (let i = 1; i <= range; i++) {
          const tempDate = mFromDate.addDays(1).toString('yyyy-MM-dd')
          if (i < range) {
            markedDates[tempDate] = { color: props.theme.markColor, textColor: props.theme.markTextColor }
          } else {
            markedDates[tempDate] = { endingDay: true, color: props.theme.markColor, textColor: props.theme.markTextColor }
          }
        }
      }
    }
    return [markedDates, range]
  }

  const setupInitialRange = () => {
    if (!props.initialRange) return
    const [fromDate, toDate] = props.initialRange
    const markedDates = { [fromDate]: { startingDay: true, color: props.theme.markColor, textColor: props.theme.markTextColor } }
    const [mMarkedDates, range] = setupMarkedDates(fromDate, toDate, markedDates)
    setState({ markedDates: mMarkedDates, fromDate })
  }

  return (
    <CalendarList
      {...props}
      firstDay={1}
      markingType="period"
      current={state.fromDate}
      markedDates={state.markedDates}
      pastScrollRange={12}
      futureScrollRange={12}
      scrollEnabled={true}
      onDayPress={(day) => { onDayPress(day) }}
    />
  )
}

