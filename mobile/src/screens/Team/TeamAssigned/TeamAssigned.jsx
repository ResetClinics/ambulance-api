import React from 'react'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { Layout, TeamList } from '../../../components'
import { COLORS } from '../../../../constants'

export const TeamAssigned = ({ navigation }) => (
  <Layout>
    <TeamList />
    <View style={styles.btnHolder}>
      <Button mode="outlined" raised onPress={() => navigation.navigate('Главная Бригады')}>
        Бригада не готова к дежурству
      </Button>
      <Button mode="contained" style={styles.btn} onPress={() => navigation.navigate('Подвержденная Бригада')}>
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
