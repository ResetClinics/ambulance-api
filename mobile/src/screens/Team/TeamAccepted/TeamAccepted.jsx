import React from 'react'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { Layout, TeamList} from '../../../components'

export const TeamAccepted = ({ navigation }) => (
  <Layout>
    <TeamList />
    <View style={styles.btnHolder}>
      <Button mode="outlined" onPress={() => navigation.navigate('Главная Бригады')}>Завершить смену</Button>
    </View>
  </Layout>
)

const styles = StyleSheet.create({
  btnHolder: {
    marginTop: 'auto', paddingTop: 16
  },
})
