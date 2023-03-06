import React from 'react'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { Layout, TeamList } from '../../../components'
import { COLORS } from '../../../../constants'

export const TeamAssigned = ({
  administrator,
  doctors,
  rejectTeam,
  acceptTeam,
  refreshing,
  onRefresh
}) => (
  <Layout>
    <TeamList
      administrator={administrator}
      doctors={doctors}
      refreshing={refreshing}
      onRefresh={onRefresh}
    />
    <View style={styles.btnHolder}>
      <Button mode="outlined" raised onPress={rejectTeam}>
        Бригада не готова к дежурству
      </Button>
      <Button mode="contained" style={styles.btn} onPress={acceptTeam}>
        Бригада вышла на дежурство
      </Button>
    </View>
  </Layout>
)

const styles = StyleSheet.create({
  btnHolder: {
    marginTop: 'auto',
    paddingTop: 16,
    paddingHorizontal: 16,
    borderTopWidth: 1,
    borderTopColor: COLORS.light,
    marginLeft: -16,
    marginRight: -16
  },
  btn: {
    marginTop: 16
  }
})
