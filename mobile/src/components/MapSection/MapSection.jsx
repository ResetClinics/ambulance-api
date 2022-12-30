import React from "react";
import { StyleSheet, Text, View } from "react-native";
import MapView from 'react-native-maps';
import { Button } from "react-native-paper";
import { COLORS, FONTS } from "../../../constants";

export const MapSection = () => {
  return (
    <>
      <View style={styles.wrap}>
        <Text style={styles.title}>Расстояние до цели:</Text>
        <Text style={styles.value}>17 км</Text>
      </View>
      <View style={styles.wrap}>
        <Text style={styles.title}>Примерное время в пути:</Text>
        <Text style={styles.value}>32 мин.</Text>
      </View>
      <View style={styles.container}>
        <MapView style={styles.map}
           initialRegion={{
             latitude: 55.755811,
             longitude: 37.617617,
             latitudeDelta: 0.0922,
             longitudeDelta: 0.0421,
           }}
        />
      </View>
      <Button mode="outlined" style={styles.btn}>Редактировать маршрут</Button>
    </>
  );
}

const styles = StyleSheet.create({
  wrap: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 16
  },
  title: {
    ...FONTS.smallTitle
  },
  value: {
    ...FONTS.smallTitle,
    color: COLORS.primary,
    marginLeft: 5
  },
  container: {
    flex: 1,
  },
  map: {
    width: '100%',
    height: '100%',
    /* ...StyleSheet.absoluteFillObject,*/
    borderRadius: 4,
    overflow: 'hidden'
  },
  btn: {
    marginTop: 16
  }
});
