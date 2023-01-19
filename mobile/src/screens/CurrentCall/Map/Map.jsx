import React from 'react'
import { Text, View } from 'react-native'
import MapView from 'react-native-maps'
import { Button } from 'react-native-paper'
import { Layout } from '../../../components'
import { styles } from './styles'

export const Map = () => (
  <Layout>
    <View style={styles.container}>
      <MapView
        style={styles.map}
        initialRegion={{
          latitude: 55.755811,
          longitude: 37.617617,
          latitudeDelta: 0.0922,
          longitudeDelta: 0.0421,
        }}
      />
    </View>
    <View style={styles.wrap}>
      <Text style={styles.title}>Расстояние до цели:</Text>
      <Text style={styles.value}>17 км</Text>
    </View>
    <View style={styles.wrap}>
      <Text style={styles.title}>Примерное время в пути:</Text>
      <Text style={styles.value}>32 мин.</Text>
    </View>
    <Button mode="outlined" style={styles.btn}>Редактировать маршрут</Button>
  </Layout>
)
