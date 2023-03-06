import React from 'react'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { Layout, TeamList } from '../../../components'
import { COLORS } from '../../../../constants'

export const TeamAccepted = ({
  administrator,
  doctors,
  completeTeam,
  refreshing,
  onRefresh,
}) => (
  <Layout>
    <TeamList
      administrator={administrator}
      doctors={doctors}
      refreshing={refreshing}
      onRefresh={onRefresh}
    />
    <View style={styles.btnHolder}>
      <Button mode="outlined" onPress={completeTeam}>Завершить смену</Button>
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
})
